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
    public function it_does_something_awesome()
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
    }
}
