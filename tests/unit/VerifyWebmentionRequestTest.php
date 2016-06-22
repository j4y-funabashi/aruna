<?php

namespace Test;

use Aruna\VerifyWebmentionRequest;

/**
 * Class VerifyWebmentionRequestTest
 * @author yourname
 */
class VerifyWebmentionRequestTest extends UnitTest
{
    public function setUp()
    {
        $this->SUT = new VerifyWebmentionRequest();
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_if_source_and_target_are_missing()
    {
        $mention = array();
        $result = $this->SUT->__invoke($mention);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_if_source_is_missing()
    {
        $mention = array("target" => "test");
        $result = $this->SUT->__invoke($mention);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_if_target_is_missing()
    {
        $mention = array("source" => "test");
        $result = $this->SUT->__invoke($mention);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_if_source_is_target()
    {
        $mention = array("source" => "test", "target" => "test");
        $result = $this->SUT->__invoke($mention);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_if_source_is_not_a_valid_url()
    {
        $mention = array(
            "source" => "test1.com",
            "target" => "test"
        );
        $result = $this->SUT->__invoke($mention);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_if_target_is_not_a_valid_url()
    {
        $mention = array(
            "source" => "http://test1.com",
            "target" => "test.com"
        );
        $result = $this->SUT->__invoke($mention);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_exception_if_target_host_is_not_my_host()
    {
        $mention = array(
            "source" => "http://example.com",
            "target" => "http://test.com"
        );
        $result = $this->SUT->__invoke($mention);
    }

    /**
     * @test
     */
    public function it_returns_mention_if_everything_looks_valid()
    {
        $mention = array(
            "source" => "http://example.com",
            "target" => "http://j4y.co"
        );
        $result = $this->SUT->__invoke($mention);
        $this->assertEquals($mention, $result);
    }
}
