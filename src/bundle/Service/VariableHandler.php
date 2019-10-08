<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler as TypeHandler;
use eZ\Publish\SPI\Persistence\Content\Type;
use ContextualCode\EzPlatformContentVariables\Variable\Value\Callback as ValueCallback;
use ContextualCode\EzPlatformContentVariables\Variable\Value\Processor as CallbackProcessor;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable;

class VariableHandler
{
    /** @var ObjectRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ContentService */
    private $contentService;

    /** @var TypeHandler */
    private $typeHandler;

    /** @var CallbackProcessor */
    private $callbackProcessor;

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager,
        ContentService $contentService,
        TypeHandler $typeHandler,
        CallbackProcessor $callbackProcessor
    ) {
        $this->repository = $doctrine->getRepository(Variable::class);
        $this->entityManager = $entityManager;
        $this->contentService = $contentService;
        $this->typeHandler = $typeHandler;
        $this->callbackProcessor = $callbackProcessor;
    }

    public function find(int $id): ?Variable
    {
        return $this->repository->find($id);
    }

    public function findByCollection(Collection $collection): array
    {
        return $collection->getContentVariables()->getValues();
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function persist(Variable $variable): void
    {
        $this->entityManager->persist($variable);
        $this->entityManager->flush();
    }

    public function delete(Variable $variable): void
    {
        $this->entityManager->remove($variable);
        $this->entityManager->flush();
    }

    public function linkedContentInfo(Variable $variable): array
    {
        $linkedContent = [];

        $placeholder = $variable->getPlaceholder();
        if ($placeholder === null) {
            return $linkedContent;
        }

        $query = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('DISTINCT contentobject_id, version, contentclassattribute_id')
            ->from('ezcontentobject_attribute')
            ->where('data_text LIKE :contnet_variable')
            ->setParameter('contnet_variable', '%' . $placeholder . '%')
            ->orderBy('contentobject_id', 'DESC')
            ->addOrderBy('version', 'DESC');

        $fieldNames = [];
        $result = $query->execute();
        while ($row = $result->fetch()) {
            $id = (int) $row['contentobject_id'];
            $version = (int) $row['version'];
            $content = $this->contentService->loadContent($id, null, $version);

            $fieldId = (int) $row['contentclassattribute_id'];
            if (isset($fieldNames[$fieldId]) === false) {
                $fieldNames[$fieldId] = $this->getFieldName($fieldId);
            }

            $linkedContent[] = [
                'content' => $content,
                'field_name' => $fieldNames[$fieldId]
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
        $name = isset($names[$language]) ? $names[$language] : $fieldDefinition->identifier;

        return $name . ' (' . $fieldDefinition->fieldType . ')';
    }

    public function getVariableValue(Variable $variable): ?string
    {
        if ($variable->isStatic()) {
            return $variable->getValueStatic();
        }

        $callback = $this->callbackProcessor->getCallback($variable->getValueCallback());
        if ($callback instanceof ValueCallback) {
            return $callback->getValue();
        }

        return null;
    }
}
