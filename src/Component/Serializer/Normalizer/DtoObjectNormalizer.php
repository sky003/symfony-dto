<?php

declare(strict_types = 1);

namespace App\Component\Serializer\Normalizer;

use App\Dto\Assembler\DtoNormalizerFactoryInterface;
use App\Dto\Request\DtoResourceInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * A custom normalizer for the DTO objects.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class DtoObjectNormalizer extends GetSetMethodNormalizer
{
    /**
     * @var DtoNormalizerFactoryInterface
     */
    private $dtoNormalizerFactory;

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, DtoNormalizerFactoryInterface $dtoNormalizerFactory)
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyTypeExtractor);

        $this->dtoNormalizerFactory = $dtoNormalizerFactory;
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    Data to restore
     * @param string $class   The expected class to instantiate
     * @param string $format  Format the given data was extracted from
     * @param array  $context Options available to the denormalizer
     *
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = parent::denormalize($data, $class, $format, $context);

        // Just return a denormalized object if it isn't a DTO object
        // or its identifier is empty (that probably a new object if identifier is empty).
        if (!$object instanceof DtoResourceInterface || !isset($context['dto_id']) || null === $context['dto_id']) {
            return $object;
        }

        $object->setId((int) $context['dto_id']);

        return $this->dtoNormalizerFactory->getDtoInitializer($object)->initializeDto();
    }
}
