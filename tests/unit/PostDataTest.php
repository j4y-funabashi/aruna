<?php

namespace Test;

class PostDataTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new \Aruna\Micropub\PostData();
        $this->expected = [
            "items" => [
                0 => [
                    "type" => ["h-entry"],
                    "properties" => [
                        "uid" => [1],
                        "url" => ["/p/1"],
                        "photo" => ["/2016/test.jpg"],
                        "author" => [
                            [
                                "type" => ["h-card"],
                                "properties" => [
                                    "name" => ["Jay Robinson"],
                                    "photo" => ["/profile_pic.jpeg"],
                                    "url" => ["https://j4y.co"]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function it_removes_access_token()
    {
        $post_data = ["uid" => 1, "access_token" => 1, "files" => ["photo" => "2016/test.jpg"]];
        $result = $this->SUT->toMfArray($post_data);
        $this->assertEquals($this->expected, $result);
    }
}
