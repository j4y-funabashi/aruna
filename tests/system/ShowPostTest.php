<?php

namespace Test;

/**
 * Class ShowPostTest
 * @author yourname
 */
class ShowPostTest extends SystemTest
{
    /**
     * @test
     */
    public function it_shows_post_with_correct_microformats()
    {
        $this->insertValidPost();
        $result = $this->http->get("http://localhost/p/1234");

        $mf_array = \Mf2\parse((string) $result->getBody(), "http://localhost");
        $this->assertContains($this->getValidPostArray(), $mf_array);
        $this->assertEquals(200, $result->getStatusCode());
    }
}
