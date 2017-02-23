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
                "uid" => ["test_uid"],
                "category" => ["hello"]
            ]
        ];
        $result = $this->SUT->__invoke($post);
        $expected = [
            "properties" => [
                "uid" => ["test_uid"],
                "category" => ["hello"]
            ],
            "sql_statements" => [
                [
                    "REPLACE INTO tags (id, tag) VALUES (?, ?)",
                    ["5d41402abc4b2a76b9719d911017c592", "hello"]
                ],
                [
                    "REPLACE INTO posts_tags (post_id, tag_id) VALUES (?, ?)",
                    ["test_uid","5d41402abc4b2a76b9719d911017c592"]
                ]
            ]
        ];
        $this->assertEquals($result, $expected);
    }
}
