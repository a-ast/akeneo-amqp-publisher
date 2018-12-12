<?php

namespace Aa\AkeneoImport\Serializer;

use Aa\AkeneoImport\ImportCommands\CommandList;
use Aa\AkeneoImport\ImportCommands\Product\UpdateProduct;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class CommandListNormalizer implements DenormalizerInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $commands = [];

        foreach ($data as $item) {
            $commands[] = $this->denormalizer->denormalize($item, UpdateProduct::class, $format, $context);
        }

        return new CommandList($commands);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'json' === $format && CommandList::class === $type;
    }
}
