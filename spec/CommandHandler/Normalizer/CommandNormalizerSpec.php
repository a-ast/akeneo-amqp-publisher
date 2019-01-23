<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Normalizer;

use Aa\AkeneoImport\CommandHandler\Normalizer\CommandNormalizer;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommandNormalizerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CommandNormalizer::class);
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(NormalizerAwareInterface::class);
    }

    function it_normalizes_a_command(NormalizerInterface $baseNormalizer)
    {
        $this->beConstructedWith(['old-code' => 'new-code']);

        $baseNormalizer->normalize(Argument::any(), Argument::any(), Argument::any())->willReturn([
            'old-code' => 1, 'color' => 'red'
        ]);

        $this->setNormalizer($baseNormalizer);

        $this->normalize(Argument::any(), Argument::any(), [])->shouldBeLike([
            'new-code' => 1, 'color' => 'red'
        ]);
    }

    function it_supports_normalization_for_commands(CommandInterface $command)
    {
        $this->supportsNormalization($command, 'standard')->shouldBe(true);
    }

    function it_does_not_support_normalization_for_non_commands()
    {
        $this->supportsNormalization(new class {}, Argument::any())->shouldBe(false);
    }

}
