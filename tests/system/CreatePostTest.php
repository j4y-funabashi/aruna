<?php

namespace Test;

class CreatePostTest extends SystemTest
{

    /**
     * @test
     */
    public function it_returns_unauthorized_when_token_is_invalid()
    {
        $config = [
            "form_params" => [
                "h" => "entry"
            ],
            "http_errors" => false
        ];
        $res = $this->app["http_client"]->request(
            "POST",
            $this->base_url."/micropub",
            $config
        );
        $expected = 401;
        $this->assertEquals($expected, $res->getStatusCode());
    }

    /**
     * @test
     */
    public function it_creates_a_post_when_token_is_valid()
    {
        $config = [
            "form_params" => [
                "h" => "entry"
            ],
            "headers" => [
                "Authorization" => getenv("AUTH_TOKEN")
            ],
            "http_errors" => false
        ];
        $res = $this->app["http_client"]->request(
            "POST",
            $this->base_url."/micropub",
            $config
        );
        $this->assertTrue($res->hasHeader("Location"));
        $url = parse_url($res->getHeader("Location")[0]);
        $this->assertArrayHasKey("host", $url);
        $this->assertArrayHasKey("scheme", $url);
        $this->assertArrayHasKey("path", $url);
        $this->assertEquals(202, $res->getStatusCode());
    }
}
