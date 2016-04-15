<?php

namespace Test;

use Aruna\Pipeline\ParseCategories;

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
    public function it_does_nothing_if_event_has_no_category()
    {
        $event = array();
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($event, $result);
    }

    /**
    * @test
    */
    public function it_parses_events_with_an_array_of_categories()
    {
        $event = array(
            'category' => array('test1', 'test2')
        );
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($event, $result);
    }

    /**
    * @test
    */
    public function it_parses_events_with_a_csv_of_categories()
    {
        $event = array(
            'category' => 'test1,test2'
        );
        $expected = array(
            'category' => array('test1', 'test2')
        );
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
    * @test
    */
    public function it_trims_categories()
    {
        $event = array(
            'category' => array(' test1 ', ' test2')
        );
        $expected = array(
            'category' => array('test1', 'test2')
        );
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
    * @test
    */
    public function it_converts_categories_to_lowercase()
    {
        $event = array(
            'category' => array(' tEsT1 ', ' TEST2')
        );
        $expected = array(
            'category' => array('test1', 'test2')
        );
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
    * @test
    */
    public function it_dedupes_categories()
    {
        $event = array(
            'category' => 'test1,test2,test1'
        );
        $expected = array(
            'category' => array('test1', 'test2')
        );
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }

    /**
    * @test
    */
    public function it_filters_out_empty_categories()
    {
        $event = array(
            'category' => array("", null, " ")
        );
        $expected = array();
        $result = $this->SUT->__invoke($event);
        $this->assertEquals($expected, $result);
    }
}
