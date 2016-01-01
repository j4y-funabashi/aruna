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
    public function it_throws_exception_if_h_is_invalid()
    {
        $config = [
            "h" => "blahhahah"
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
            "anything" => "else"
        ];
        $SUT = new Entry($config);

        $this->assertEquals(
            '{"published":"2016-01-01 01:00:00","anything":"else"}',
            json_encode($SUT)
        );
    }
}
