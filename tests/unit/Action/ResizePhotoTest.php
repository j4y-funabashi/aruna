<?php

namespace Test;

use Aruna\Action\ResizePhoto;

/**
 * Class ResizePhotoTest
 * @author yourname
 */
class ResizePhotoTest extends UnitTest
{
    public function setUp()
    {
        $this->resizer = $this->prophesize("\Aruna\Action\ImageResizer");
        $this->log = $this->prophesize("\Monolog\Logger");
        $this->SUT = new ResizePhoto(
            $this->log->reveal(),
            $this->resizer->reveal()
        );
    }

    /**
     * @test
     */
    public function it_does_nothing_if_post_has_no_photo()
    {
        $post = ["h" => "entry"];
        $result = call_user_func($this->SUT, $post);
        $this->assertEquals($post, $result);
    }

    /**
     * @test
     */
    public function it_passes_photo_to_resizer_if_available()
    {
        $post = [
            "h" => "entry",
            "files" => [
                "photo" => "test.jpg"
            ]
        ];
        $this->resizer->resize($post['files']['photo'])
            ->shouldBeCalled();
        $result = call_user_func($this->SUT, $post);
        $this->assertEquals($post, $result);
    }
}
