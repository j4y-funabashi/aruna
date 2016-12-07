<?php

namespace Test;

use Aruna\Micropub\FilterPostProperties;

class FilterPostPropertiesTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new FilterPostProperties();
        $this->post = [
            "type" => ["h-test"],
            "properties" => [
                "content" => ["hello"],
                "category" => ["test"]
            ]
        ];
    }

    /**
     * @test
     */
    public function it_returns_full_post_if_properties_is_empty()
    {
        $properties = [];
        $result = $this->SUT->__invoke($this->post, $properties);
        $this->assertEquals($this->post, $result);
    }

    /**
     * @test
     */
    public function it_returns_filtered_post_containing_properties_specified()
    {
        $properties = ["content"];
        $expected = [
            "properties" => [
                "content" => ["hello"]
            ]
        ];
        $result = $this->SUT->__invoke($this->post, $properties);
        $this->assertEquals($expected, $result);
    }
}
