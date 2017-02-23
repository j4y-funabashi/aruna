<?php

namespace Test;

use Aruna\Publish\ParseCategories;

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
    public function it_splits_tags_by_comma()
    {
        $post = array("properties" => ["category" => ["test ,test2","test3", "test,Test5"]]);
        $expected = array("properties" => ["category" => ["test","test2","test3","test5"]]);
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($expected, $result);
    }
}
