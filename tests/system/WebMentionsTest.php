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
    public function it_recieves_valid_webmention_post()
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

        $this->assertEquals(201, $response->getStatusCode());
        $mention_url = trim($response->getBody());
    }
}
