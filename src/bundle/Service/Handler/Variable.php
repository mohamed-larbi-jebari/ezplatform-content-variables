<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\Service\Handler;

use ContextualCode\EzPlatformContentVariables\Variable\Value\Processor as CallbackProcessor;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Collection;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Entity;
use ContextualCode\EzPlatformContentVariablesBundle\Entity\Variable as VariableEntity;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\FullText;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine\ORM\EntityRepository as ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Persistence\Legacy\Content\Type\Handler as TypeHandler;
use Ibexa\Contracts\Core\Persistence\Content\Type;

class Variable extends Handler
{
    /** @var ContentService */
    protected $contentService;

    /** @var TypeHandler */
    protected $typeHandler;

    /** @var CallbackProcessor */
    protected $callbackProcessor;

    /** @var SearchService */
    protected $searchService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContentService         $contentService,
        TypeHandler            $typeHandler,
        CallbackProcessor      $callbackProcessor,
        SearchService          $searchService
    ) {
        parent::__construct($entityManager);

        $this->contentService = $contentService;
        $this->typeHandler = $typeHandler;
        $this->callbackProcessor = $callbackProcessor;
        $this->searchService = $searchService;
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository(VariableEntity::class);
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

    public function countLinkedContent(VariableEntity $variable): void
    {
        $placeholder = $variable->getPlaceholder();

        if ($placeholder === null) {
            return;
        }
        $criterion = new Criterion\CustomField(
            'meta_content__text_t',
            Criterion\Operator::CONTAINS,
            "{$placeholder}"
        );
        $query = new LocationQuery(['query' => $criterion, 'limit' => 0]);
        $results = $this->searchService->findContentInfo($query);

        $variable->setLinkedContentCount((int) $results->totalCount);
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
            $id = (int) $row['contentobject_id'];
            $version = (int) $row['version'];
            $content = $this->contentService->loadContent($id, null, $version);

            $fieldId = (int) $row['contentclassattribute_id'];
            if (!isset($fieldNames[$fieldId])) {
                $fieldNames[$fieldId] = $this->getFieldName($fieldId);
            }

            $linkedContent[] = [
                'content' => $content,
                'field' => [
                    'id' => $fieldId,
                    'name' => $fieldNames[$fieldId],
                ],
            ];
        }

        return $linkedContent;
    }

    public function linkedContentInfoGrouped(VariableEntity $variable): array
    {
        $return = [
            VersionInfo::STATUS_PUBLISHED => [],
            VersionInfo::STATUS_ARCHIVED => [],
            VersionInfo::STATUS_DRAFT => [],
        ];

        $linkedContent = $this->linkedContentInfo($variable);
        foreach ($linkedContent as $link) {
            $key = $link['content']->id . '-' . $link['content']->versionInfo->versionNo;
            $status = $link['content']->versionInfo->status;

            if (isset($return[$status]) === false) {
                continue;
            }

            if (isset($return[$status][$key]) === false) {
                $return[$status][$key] = [
                    'content' => $link['content'],
                    'fields' => [$link['field']],
                ];
            } else {
                $return[$status][$key]['fields'][] = $link['field'];
            }
        }

        return $return;
    }

    protected function getFieldName(int $id): string
    {
        try {
            $fieldDefinition = $this->typeHandler->getFieldDefinition($id, Type::STATUS_DEFINED);
        } catch (NotFoundException) {
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

        /** @var Callback $callback */
        $callback = $this->callbackProcessor->getCallback($variable->getValueCallback());
        if ($callback) {
            return $callback->getValue();
        }

        return null;
    }

    public function missingCallbackToStatic(VariableEntity $variable): bool
    {
        if ($variable->isStatic()) {
            return false;
        }

        $callback = $variable->getValueCallback();
        if ($this->callbackProcessor->getCallback($callback) === null) {
            $variable->makeStatic();
            $this->persist($variable);

            return true;
        }

        return false;
    }
}
