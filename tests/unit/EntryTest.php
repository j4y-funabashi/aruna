<?php

namespace Test;

use Aruna\Entry;

/**
 * Class EntryTest
 * @author yourname
 */
class EntryTest extends UnitTest
{

    /**
    * @test
    * @expectedException RuntimeException
    */
    public function it_throws_exception_if_h_is_not_entry()
    {
        $config = [
            "h" => "not_entry"
        ];
        $SUT = new Entry($config);
    }

    /**
    * @test
    * @expectedException RuntimeException
    */
    public function it_throws_exception_if_date_is_invalid()
    {
        $config = [
            "h" => "entry",
            "published" => "2wko1"
        ];
        $SUT = new Entry($config);
    }

    /**
    * @test
    * @expectedException RuntimeException
    */
    public function it_throws_exception_if_content_or_photo_are_null()
    {
        $config = [
            "h" => "entry"
        ];
        $SUT = new Entry($config);
    }

    /**
     * @test
     */
    public function it_has_a_json_representation()
    {
        $config = [
            "h" => 'entry',
            "published" => "2016-01-01 01:00:00",
            "content" => "yo",
            "anything" => "else"
        ];
        $SUT = new Entry($config);

        $this->assertEquals(
            '{"published":"2016-01-01T01:00:00+00:00","content":"yo","anything":"else"}',
            json_encode($SUT)
        );
    }
}
