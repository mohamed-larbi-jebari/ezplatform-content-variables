<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service\Handler;

use ContextualCode\EzPlatformContentVariables\Variable\Value\Processor as CallbackProcessor;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Entity;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable as VariableEntity;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler as TypeHandler;
use eZ\Publish\SPI\Persistence\Content\Type;

class Variable extends Handler
{
    /** @var ContentService */
    protected $contentService;

    /** @var TypeHandler */
    protected $typeHandler;

    /** @var CallbackProcessor */
    protected $callbackProcessor;

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager,
        ContentService $contentService,
        TypeHandler $typeHandler,
        CallbackProcessor $callbackProcessor
    ) {
        parent::__construct($doctrine, $entityManager);

        $this->contentService = $contentService;
        $this->typeHandler = $typeHandler;
        $this->callbackProcessor = $callbackProcessor;
    }

    protected function getRepository(ManagerRegistry $doctrine): ObjectRepository
    {
        return $doctrine->getRepository(VariableEntity::class);
    }

    public function findByCollection(Collection $collection): array
    {
        $criteria = ['collection' => $collection];
        return $this->repository->findBy($criteria, $this->getOrderBy());
    }

    public function persist(Entity $variable): void
    {
        $variable->fixStaticValuePlaceholder();

        parent::persist($variable);
    }

    public function linkedContentInfo(VariableEntity $variable): array
    {
        $linkedContent = [];

        $placeholder = $variable->getPlaceholder();
        if ($placeholder === null) {
            return $linkedContent;
        }

        $query = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('DISTINCT contentobject_id, version, contentclassattribute_id')
            ->from('ezcontentobject_attribute')
            ->where('data_text LIKE :content_variable')
            ->setParameter('content_variable', '%' . $placeholder . '%')
            ->orderBy('contentobject_id', 'DESC')
            ->addOrderBy('version', 'DESC');

        $fieldNames = [];
        $result = $query->execute();
        while ($row = $result->fetch()) {
            $id = (int)$row['contentobject_id'];
            $version = (int)$row['version'];
            $content = $this->contentService->loadContent($id, null, $version);

            $fieldId = (int)$row['contentclassattribute_id'];
            if (isset($fieldNames[$fieldId]) === false) {
                $fieldNames[$fieldId] = $this->getFieldName($fieldId);
            }

            $linkedContent[] = [
                'content' => $content,
                'field_name' => $fieldNames[$fieldId],
            ];
        }

        return $linkedContent;
    }

    protected function getFieldName(int $id): string
    {
        try {
            $fieldDefinition = $this->typeHandler->getFieldDefinition($id, Type::STATUS_DEFINED);
        } catch (NotFoundException $e) {
            return $id;
        }

        $language = $fieldDefinition->mainLanguageCode;
        $names = $fieldDefinition->name;
        $name = $names[$language] ?? $fieldDefinition->identifier;

        return $name . ' (' . $fieldDefinition->fieldType . ')';
    }

    public function getVariableValue(VariableEntity $variable): ?string
    {
        if ($variable->isStatic()) {
            return $variable->getValueStatic();
        }

        $callback = $this->callbackProcessor->getCallback($variable->getValueCallback());
        if ($callback) {
            return $callback->getValue();
        }

        return null;
    }
}
