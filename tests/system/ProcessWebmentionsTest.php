<?php

namespace Test;

class ProcessWebmentionsTest extends SystemTest
{

    /**
     * @test
     */
    public function it_awesome()
    {
        $this->addWebmention();
        $this->SUT = $this->app['action.process_webmentions'];
        $expected = 1;
        $result = $this->SUT->__invoke();
        $this->assertEquals($expected, $result['count']);
    }
}
