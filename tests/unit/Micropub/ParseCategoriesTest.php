<?php

namespace Test;

use Aruna\Micropub\ParseCategories;
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
        $post = array();
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($post, $result);
    }

    /**
    * @test
    */
    public function it_does_nothing_if_post_has__categories_but_no_person_tags()
    {
        $post = array("category" => ["test"]);
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($post, $result);
    }

    /**
    * @test
    */
    public function it_replaces_person_tag_with_hcard()
    {
        $post = array("category" => ["@test"]);
        $expected = array(
            "category" => array(
                [
                    "type" => ["h-card"],
                    "properties" => [
                        "name" => ["@test"],
                        "url" => ["test"]
                    ]
                ]
        )
        );

        $result = $this->SUT->__invoke($post);
        $this->assertEquals($expected, $result);
    }
}
