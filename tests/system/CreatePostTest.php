<?php

namespace Test;

/**
 * Class CreatePostTest
 * @author yourname
 */
class CreatePostTest extends SystemTest
{
    /**
    * @test
    */
    public function it_returns_unauthorized_401_if_access_token_is_invalid()
    {
        $result = $this->http->post("http://localhost/micropub");
        $this->assertEquals(401, $result->getStatusCode());
    }
}
