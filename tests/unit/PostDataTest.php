<?php

namespace Test;

class PostDataTest extends UnitTest
{

    /**
     * @test
     */
    public function it_does_awesome()
    {

        $SUT = new \Aruna\PostData();

        $post_data = ["uid" => 1];
        $result = $SUT::toMfArray($post_data);
        $expected = [
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
        $this->assertEquals($expected, $result);
    }
}
