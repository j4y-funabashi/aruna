<?php

namespace Test;

use Aruna\Pipeline\ParseCategories;
use Aruna\PostViewModel;

/**
 * Class ParseCategoriesTest
 * @author yourname
 */
class ParseCategoriesTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new ParseCategories();
    }

    /**
    * @test
    */
    public function it_does_nothing_if_post_has_no_category()
    {
        $mf_array = array(
            "items" => [
                [
                    "type" => ["h-entry"]
                ]
            ]
        );
        $post = new PostViewModel($mf_array);
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($post, $result);
    }

    /**
    * @test
    */
    public function it_does_nothing_if_post_has__categories_but_no_person_tags()
    {
        $mf_array = array(
            "items" => [
                [
                    "type" => ["h-entry"],
                    "properties" => [
                        "category" => ["test"]
                    ]
                ]
            ]
        );
        $post = new PostViewModel($mf_array);
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($post, $result);
    }

    /**
    * @test
    */
    public function it_replaces_person_tag_with_hcard()
    {
        $mf_array = array(
            "items" => [
                [
                    "type" => ["h-entry"],
                    "properties" => [
                        "category" => ["@test"]
                    ]
                ]
            ]
        );
        $post = new PostViewModel($mf_array);

        $mf_array = array(
            "items" => [
                [
                    "type" => ["h-entry"],
                    "properties" => [
                        "category" => [
                            [
                                "type" => ["h-card"],
                                "properties" => [
                                    "name" => ["@test"],
                                    "url" => ["test"]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        $expected = new PostViewModel($mf_array);

        $post = $this->SUT->__invoke($post);
        $this->assertEquals($expected->category(), $post->category());
    }
}
