<?php

namespace Test;

use Aruna\Micropub\CreatePostCommand;
use Aruna\Micropub\UploadedFile;

class CreatePostCommandTest extends UnitTest
{

    /**
     * @test
     */
    public function it_filters_out_access_token_from_post()
    {
        $SUT = new CreatePostCommand(
            $entry = ["access_token" => "test"],
            $files = [],
            $token = ""
        );
        $expected = [];
        $result = $SUT->getEntry();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_only_returns_valid_uploaded_files()
    {
        $invalid_file = new UploadedFile(
            $real_path = '',
            $original_ext = '',
            $is_readable = false,
            $is_valid = false
        );
        $SUT = new CreatePostCommand(
            $entry = [],
            $files = [$invalid_file],
            $token = ""
        );

        $expected = [];
        $result = $SUT->getFiles();
        $this->assertEquals($expected, $result);
    }
}
