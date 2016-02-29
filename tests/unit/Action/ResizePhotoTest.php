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
        $this->resizer = $this->prophesize("\Aruna\ImageResizer");
    }

    /**
     * @test
     */
    public function it_does_nothing_if_post_has_no_photo()
    {
        $post = ["h" => "entry"];
        $SUT = new ResizePhoto(
            $this->resizer->reveal()
        );
        $result = $SUT($post);
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
        $SUT = new ResizePhoto(
            $this->resizer->reveal()
        );
        $this->resizer->resize($post['files']['photo'])
            ->shouldBeCalled();
        $result = $SUT($post);
        $this->assertEquals($post, $result);
    }
}
