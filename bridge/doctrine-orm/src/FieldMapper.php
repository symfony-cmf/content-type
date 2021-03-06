<?php

declare(strict_types=1);

namespace Psi\Bridge\ContentType\Doctrine\Orm;

use Doctrine\ORM\Mapping\ClassMetadata;
use Psi\Component\ContentType\Field;
use Psi\Component\ContentType\FieldLoader;
use Psi\Component\ContentType\FieldOptions;
use Psi\Component\ContentType\Standard\Storage as Type;

class FieldMapper
{
    private $fieldLoader;

    public function __construct(FieldLoader $fieldLoader)
    {
        $this->fieldLoader = $fieldLoader;
    }

    public function __invoke($fieldName, Field $field, ClassMetadata $metadata, $extraOptions = [])
    {
        $type = $field->getStorageType();
        $options = $field->getStorageOptions();
        $extraOptions = array_merge([
            'serialize_scalar' => false,
        ], $extraOptions);

        if (true === $extraOptions['serialize_scalar']) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'array',
            ]);

            return;
        }

        if ($type === Type\ObjectType::class) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'object',
            ]);

            return;
        }

        if ($type === Type\BooleanType::class) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'boolean',
                'nullable' => true,
            ]);

            return;
        }

        if ($type === Type\DoubleType::class) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'float',
                'nullable' => true,
            ]);

            return;
        }

        if ($type === Type\CollectionType::class) {
            $this->mapCollectionType($fieldName, $field, $metadata);

            return;
        }

        if ($type === Type\StringType::class) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'string',
                'nullable' => true,
            ]);

            return;
        }

        if ($type === Type\IntegerType::class) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'integer',
                'nullable' => true,
            ]);

            return;
        }

        if ($type === Type\DateTimeType::class) {
            $metadata->mapField([
                'fieldName' => $fieldName,
                'type' => 'date',
                'nullable' => true,
            ]);

            return;
        }

        if ($type === Type\ReferenceType::class) {
            if (false === isset($options['class'])) {
                throw new \InvalidArgumentException(sprintf(
                    'Doctrine ORM storage requires that you provide the "class" option for reference mapping for "%s::$%s"',
                    $metadata->getName(), $fieldName
                ));
            }

            $metadata->mapManyToOne([
                'fieldName' => $fieldName,
                'targetEntity' => $options['class'],
                'cascade' => ['all'],
            ]);

            return;
        }

        throw new \RuntimeException(sprintf(
            'Do not know how to map field of type "%s"',
            $type
        ));
    }

    private function mapCollectionType($fieldName, Field $field, ClassMetadata $metadata)
    {
        $options = $field->getOptions();
        $collectionField = $this->fieldLoader->load($options['field_type'], FieldOptions::create($options['field_options']));

        // assume that other types are scalars...
        $this->__invoke($fieldName, $collectionField, $metadata, [
            'serialize_scalar' => true,
        ]);
    }
}
