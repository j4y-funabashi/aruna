<?php

namespace Test;

use Aruna\Micropub\CreatePostAction;
use Symfony\Component\HttpFoundation\Request;
use Prophecy\Argument;
use Aruna\Found;

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
        $this->handler = $this->prophesize("\Aruna\Micropub\CreatePostHandler");
        $this->responder = $this->prophesize("\Aruna\Micropub\CreatePostResponder");

        $this->SUT = new CreatePostAction(
            $this->log,
            $this->handler->reveal(),
            $this->responder->reveal()
        );
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
        $file->method("isReadable")
            ->willReturn(true);
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
        $file->method("isValid")
            ->willReturn(true);
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
        $file->method("isReadable")
            ->willReturn(true);
        $file->method("isValid")
            ->willReturn(true);

        $request = new Request(
            $query = array(),
            $post_request = array("h" => "entry", "access_token" => 123),
            $attributes = array(),
            $cookies = array(),
            $files = array("photo" => $file)
        );
        $handler_response = new Found([]);

        $this->handler->handle(
            new \Aruna\Micropub\CreatePostCommand(
                $post_request,
                array(
                    "photo" => array(
                        'real_path' => "/test",
                        'original_ext' => "jpg"
                    )
                ),
                "123"
            )
        )->shouldBeCalled()
        ->willReturn($handler_response);

        $this->responder->setPayload($handler_response)
            ->shouldBeCalled();
        $this->responder->__invoke()
            ->shouldBeCalled();

        $response = $this->SUT->__invoke($request);
    }
}
