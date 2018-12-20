<?php

namespace Aa\AkeneoImport\Serializer;

use Aa\AkeneoImport\ImportCommands\CommandInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class CommandNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @param \Aa\AkeneoImport\ImportCommands\CommandInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        // @todo: do reflection and take it from hidden to avoid exposing internal format of getData ?

        $data = $this->normalizer->normalize($object->getData(), $format, $context);

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof CommandInterface;
    }
}
