<?php

namespace Test;

class ProcessWebmentionsTest extends SystemTest
{

    /**
     * @test
     */
    public function it_awesome()
    {
        $this->SUT = $this->app['action.process_webmentions'];
        $expected = 0;
        $result = $this->SUT->__invoke();
        $this->assertEquals($expected, $result['count']);
    }
}
