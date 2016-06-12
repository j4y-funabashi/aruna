<?php

namespace Test;

use Aruna\SendWebmention;
use Prophecy\Argument;

class SendWebmentionTest extends UnitTest
{
    public function setUp()
    {
        $this->post_uid = "1234";
        $target_url = "http://example.com";
        $mention_endpoint = "http://example.com/webmention";
        $source_url = "http://j4y.co/p/".$this->post_uid;

        $log = $this->prophesize("Monolog\Logger");

        $form_params = ["source" => $source_url, "target" => $target_url];
        $http = $this->prophesize("GuzzleHttp\Client");
        $http->request(
            "GET",
            $target_url
        )->shouldBeCalled();
        $http->request(
            "POST",
            $mention_endpoint,
            ["form_params" => $form_params]
        )->shouldBeCalled();

        $discoverEndpoint = $this->prophesize("Aruna\DiscoverEndpoints");
        $discoverEndpoint->__invoke(Argument::cetera())
            ->willReturn($mention_endpoint);

        $urls = array($target_url);
        $findUrls = $this->prophesize("Aruna\FindUrls");
        $findUrls->__invoke(Argument::cetera())
            ->willReturn($urls);

        $this->SUT = new SendWebmention(
            $http->reveal(),
            $discoverEndpoint->reveal(),
            $findUrls->reveal(),
            $log->reveal()
        );
    }

    /**
     * @test
     */
    public function it_does_awesome()
    {
        $event = array("uid" => $this->post_uid);
        $this->SUT->__invoke($event);
    }
}
