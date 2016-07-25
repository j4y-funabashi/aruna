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
        $this->SUT->__invoke();
    }
}
