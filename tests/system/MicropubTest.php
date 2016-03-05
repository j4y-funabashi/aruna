<?php

namespace Test;

use Mf2;

/**
 * Class MicropubTest
 * @author yourname
 */
class MicropubTest extends SystemTest
{

    /**
     * @test
     */
    public function it_returns_202_accepted_for_valid_webmention_post()
    {
        $response = $this->http->request(
            'POST',
            'micropub',
            [
            'form_params' => [
                'h' => 'entry',
                'title' => 'hello test'
            ]
            ]
        );

        $this->assertEquals(202, $response->getStatusCode());
    }
}
