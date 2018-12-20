<?php

namespace Aa\AkeneoImport\Serializer;

use Aa\AkeneoImport\ImportCommands\CommandList;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class CommandListNormalizer implements DenormalizerInterface, DenormalizerAwareInterface, NormalizerInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait, NormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $commands = [];

        // @todo: worth checking that $data['commandClass'] exist? Reflection?

        foreach ($data['items'] as $item) {
            $commands[] = $this->denormalizer->denormalize($item, $data['commandClass'], $format, $context);
        }

        return new CommandList($commands);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'json' === $format && CommandList::class === $type;
    }

    /**
     * @param \Aa\AkeneoImport\ImportCommands\CommandList $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = ['commandClass' => $object->getCommandClass()];

        foreach ($object->getItems() as $item) {
            $data['items'][] = $this->normalizer->normalize($item, $format, $context);
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return 'json' === $format && $data instanceof CommandList;
    }
}
