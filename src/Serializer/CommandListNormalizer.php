<?php

namespace Aa\AkeneoImport\Serializer;

use Aa\AkeneoImport\ImportCommands\CommandList;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Aa\AkeneoImport\ImportCommands\Product\UpdateProduct;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class CommandListNormalizer implements DenormalizerInterface, NormalizerInterface
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
