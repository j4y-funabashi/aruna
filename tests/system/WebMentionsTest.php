<?php

namespace Test;

/**
 * Class WebMentionsTest
 * @author yourname
 */
class WebMentionsTest extends SystemTest
{

    /**
     * @test
     */
    public function it_returns_202_accepted_for_valid_webmention_post()
    {
        $response = $this->http->request(
            'POST',
            'webmention',
            [
            'form_params' => [
                'source' => 'http://bob.host/post-by-bob',
                'target' => 'http://alice.host/post-by-alice'
            ]
            ]
        );

        $this->assertEquals(202, $response->getStatusCode());
    }
}
