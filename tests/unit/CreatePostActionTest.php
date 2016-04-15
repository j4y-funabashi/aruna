<?php

namespace Test;

use Aruna\CreatePostAction;
use Symfony\Component\HttpFoundation\Request;
use Prophecy\Argument;

/**
 * Class CreatePostActionTest
 * @author yourname
 */
class CreatePostActionTest extends UnitTest
{
    public function setUp()
    {
        $this->log = new \Monolog\Logger("test");
        $this->log->pushHandler(new \Monolog\Handler\TestHandler());
        $this->handler = $this->prophesize("\Aruna\CreatePostHandler");
        $this->token = $this->prophesize("\Aruna\AccessToken");
        $this->responder = $this->prophesize("\Aruna\CreatePostResponder");

        $this->SUT = new CreatePostAction(
            $this->log,
            $this->handler->reveal(),
            $this->token->reveal(),
            $this->responder->reveal()
        );
    }

    /**
     * @test
     */
    public function it_calls_unauthorized_method_on_responder_when_token_is_invalid()
    {
        $error_message = "test";
        $this->token->getTokenFromAuthCode(Argument::cetera())
            ->willThrow(new \Exception($error_message));

        $request = new Request();
        $response = $this->SUT->__invoke($request);

        $this->responder->unauthorized($error_message)
            ->shouldBeCalled();
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function it_will_throw_exception_for_invalid_uploaded_files()
    {
        $file = $this->getMockBuilder("\Symfony\Component\HttpFoundation\File\UploadedFile")
            ->disableOriginalConstructor()
            ->getMock();
        $file->method("isValid")
            ->willReturn(false);

        $request = new Request(
            $query = array(),
            $request = array(),
            $attributes = array(),
            $cookies = array(),
            $files = array($file),
            $server = array(),
            $content = null
        );
        $response = $this->SUT->__invoke($request);
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function it_will_throw_exception_for_unreadable_uploaded_files()
    {
        $file = $this->getMockBuilder("\Symfony\Component\HttpFoundation\File\UploadedFile")
            ->disableOriginalConstructor()
            ->getMock();
        $file->method("isReadable")
            ->willReturn(false);

        $request = new Request(
            $query = array(),
            $request = array(),
            $attributes = array(),
            $cookies = array(),
            $files = array($file),
            $server = array(),
            $content = null
        );
        $response = $this->SUT->__invoke($request);
    }

    /**
     * @test
     */
    public function it_passes_valid_post_through_to_handler_inside_a_command()
    {
        $file = $this->getMockBuilder("\Symfony\Component\HttpFoundation\File\UploadedFile")
            ->disableOriginalConstructor()
            ->getMock();
        $file->method("getRealPath")
            ->willReturn("/test");
        $file->method("getClientOriginalExtension")
            ->willReturn("jpg");

        $request = new Request(
            $query = array(),
            $request = array("h" => "entry"),
            $attributes = array(),
            $cookies = array(),
            $files = array("photo" => $file)
        );

        $this->handler->handle(
            new \Aruna\CreatePostCommand(
                array("h" => "entry"),
                array(
                    "photo" => array(
                        'real_path' => "/test",
                        'original_ext' => "jpg"
                    )
                )
            )
        )->shouldBeCalled();

        $response = $this->SUT->__invoke($request);
    }
}
