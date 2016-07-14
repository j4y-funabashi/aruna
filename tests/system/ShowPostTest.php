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
    public function it_returns_200_when_valid_post_is_in_db()
    {
        $this->insertValidPost();
        $result = $this->http->get("http://localhost/p/1234");
        $this->assertEquals(200, $result->getStatusCode());
    }
}
