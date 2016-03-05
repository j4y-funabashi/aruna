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

    /**
    * @test
    */
    public function it_returns_a_url_containing_status_of_newly_created_mention()
    {
        $post = [
            'h' => 'entry',
            'title' => 'hello test'
        ];
        $response = $this->http->request(
            'POST',
            'micropub',
            [
                'form_params' => $post
            ]
        );

        $location = $response->getHeader('Location');
        $post_url = $location[0];
        $response = $this->http->request(
            'GET',
            $post_url
        );
        $mf = Mf2\fetch($post_url);

        $this->assertEquals("h-".$post['h'], $mf['items'][0]['type'][0]);
        $this->assertEquals($post['title'], $mf['items'][0]['properties']['name'][0]);
    }
}
