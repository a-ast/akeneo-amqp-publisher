<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\ResponseValidator;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'status_code' => 422,
            'message' => 'Failed.',
            'errors' => ['1', '2'],
            'id' => '1',
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Response::class);
    }

    public function it_gets_status_code()
    {
        $this->getStatusCode()->shouldBe(422);
    }

    public function it_gets_message()
    {
        $this->getMessage()->shouldBe('Failed.');
    }

    public function it_gets_errors()
    {
        $this->getErrors()->shouldBe(['1', '2']);
    }

    public function it_gets_data()
    {
        $this->getData()->shouldBe([
            'status_code' => 422,
            'message' => 'Failed.',
            'errors' => ['1', '2'],
            'id' => '1',
        ]);
    }
}
