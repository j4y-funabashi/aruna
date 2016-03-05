<?php

namespace Test;

use Mf2;

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

    /**
    * @test
    */
    public function it_returns_a_url_containing_status_of_newly_created_mention()
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

        $mention_url = trim($response->getBody());
        $response = $this->http->request(
            'GET',
            $mention_url
        );
        $mf = Mf2\fetch(trim($response->getBody()));

        $this->assertEquals("h-entry", $mf['items'][0]['type']);
    }
}
