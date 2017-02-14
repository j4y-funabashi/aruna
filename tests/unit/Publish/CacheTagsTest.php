<?php

namespace Test;

use Aruna\Publish\CacheTags;

class CacheTagsTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new CacheTags();
    }

    /**
     * @test
     */
    public function it_does_not_process_posts_with_no_tags()
    {
        $post = ["test" => 1];
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($result, $post);
    }

    /**
     * @test
     */
    public function it_creates_sql_for_posts_with_one_tag()
    {
        $post = [
            "properties" => [
                "category" => ["hello"]
            ]
        ];
        $result = $this->SUT->__invoke($post);
        $this->assertEquals($result, $post);
    }
}
