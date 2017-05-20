<?php

namespace Test;

use \Aruna\Webmention\DiscoverWebmentionType;

class DiscoverWebmentionTypeTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new DiscoverWebmentionType();
    }

    /**
     * @test
     */
    public function it_sets_type_to_error()
    {
        $event = [
            "error" => "error",
            "another" => "thing"
        ];
        $expected = [
            "error" => "error",
            "another" => "thing",
            "type" => "error"
        ];
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_sets_type_to_homepage_when_url_does_not_end_in_slash()
    {
        $event = [
            "target" => "https://j4y.co"
        ];
        $expected = [
            "target" => "https://j4y.co",
            "type" => "homepage"
        ];
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_sets_type_to_homepage_when_url_ends_in_slash()
    {
        $event = [
            "target" => "https://j4y.co/"
        ];
        $expected = [
            "target" => "https://j4y.co/",
            "type" => "homepage"
        ];
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }


    /**
     * @test
     */
    public function it_sets_type_to_mention_by_default()
    {
        $event = [
            "target" => "https://j4y.co/p/1",
            "mf2" => [
                "items" => []
            ]
        ];
        $expected = [
            "target" => "https://j4y.co/p/1",
            "mf2" => [
                "items" => []
            ],
            "type" => "mention"
        ];
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_sets_type_to_comment()
    {
        $event = [
            "target" => "https://j4y.co/p/1",
            "mf2" => [
                "items" => [
                    [
                        "properties" => [
                            "in-reply-to" => "http://example.com"
                        ]
                    ]
                ]
            ]
        ];
        $expected = [
            "target" => "https://j4y.co/p/1",
            "mf2" => [
                "items" => [
                    [
                        "properties" => [
                            "in-reply-to" => "http://example.com"
                        ]
                    ]
                ]
            ],
            "type" => "comment"
        ];
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_sets_type_to_like()
    {
        $event = [
            "target" => "https://j4y.co/p/1",
            "mf2" => [
                "items" => [
                    [
                        "properties" => [
                            "like-of" => "http://example.com"
                        ]
                    ]
                ]
            ]
        ];
        $expected = [
            "target" => "https://j4y.co/p/1",
            "mf2" => [
                "items" => [
                    [
                        "properties" => [
                            "like-of" => "http://example.com"
                        ]
                    ]
                ]
            ],
            "type" => "like"
        ];
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }
}
