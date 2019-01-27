<?php

namespace Aa\AkeneoImport\CommandHandler\Normalizer;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use LogicException;
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

        if (!is_array($data)) {
            throw new LogicException('Normalizer must return array.');
        }

        foreach ($this->propertyNameReplaceMap as $replacingProperty => $property) {

            if (!isset($data[$replacingProperty])) {
                continue;
            }

            $data = array_merge([$property => $data[$replacingProperty]], $data);
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
