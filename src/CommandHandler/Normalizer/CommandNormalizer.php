<?php

namespace Aa\AkeneoImport\CommandHandler\Normalizer;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommandNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @var array
     */
    private $propertyNameReplaceMap;

    public function __construct(array $propertyNameReplaceMap)
    {
        $this->propertyNameReplaceMap = $propertyNameReplaceMap;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($object, null, $context);

        foreach ($this->propertyNameReplaceMap as $replacingProperty => $property) {

            if (!isset($data[$replacingProperty])) {
                continue;
            }

            $data[$property] = $data[$replacingProperty];
            unset($data[$replacingProperty]);

            break;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof CommandInterface && 'standard' === $format;
    }
}
