<?php

namespace Test;

class PostDataTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new \Aruna\PostData();
        $this->expected = [
            "items" => [
                0 => [
                    "type" => ["h-entry"],
                    "properties" => [
                        "url" => ["http://j4y.co/p/1"],
                        "author" => [
                            "type" => ["h-card"],
                            "properties" => [
                                "name" => ["Jay Robinson"],
                                "photo" => ["/profile_pic.jpeg"],
                                "url" => ["http://j4y.co"]
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
        $post_data = ["uid" => 1, "access_token" => 1];
        $result = $this->SUT->toMfArray($post_data);
        $this->assertEquals($this->expected, $result);
    }
}
