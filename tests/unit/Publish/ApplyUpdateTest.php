<?php

namespace Test;

use Aruna\Publish\ApplyUpdate;

class ApplyUpdateTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new ApplyUpdate();
        $this->post = [
            "type" => "h-entry",
            "properties" => ["content" => ["test"],"category" => ["test","test2"]]
        ];
    }

    /**
     * @test
     */
    public function it_replaces_properties()
    {
        $update = ["replace" => ["category" => ["test3"]]];
        $expected = [
            "type" => "h-entry",
            "properties" => ["content" => ["test"],"category" => ["test3"]]
        ];
        $result = $this->SUT->__invoke($this->post, $update);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_adds_properties_that_dont_exist()
    {
        $update = ["add" => ["name" => ["testname"]]];
        $expected = [
            "type" => "h-entry",
            "properties" => ["content" => ["test"],"category" => ["test","test2"],"name" => ["testname"]]
        ];
        $result = $this->SUT->__invoke($this->post, $update);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_adds_to_properties_that_exist()
    {
        $update = ["add" => ["category" => ["test3"]]];
        $expected = [
            "type" => "h-entry",
            "properties" => ["content" => ["test"],"category" => ["test","test2","test3"]]
        ];
        $result = $this->SUT->__invoke($this->post, $update);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_removes_properties()
    {
        $update = ["delete" => ["category"]];
        $expected = [
            "type" => "h-entry",
            "properties" => ["content" => ["test"]]
        ];
        $result = $this->SUT->__invoke($this->post, $update);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_removes_values_of_properties()
    {
        $update = ["delete" => ["category" => ["test2"]]];
        $expected = [
            "type" => "h-entry",
            "properties" => ["content" => ["test"], "category" => ["test"]]
        ];
        $result = $this->SUT->__invoke($this->post, $update);
        $this->assertEquals($expected, $result);
    }
}
