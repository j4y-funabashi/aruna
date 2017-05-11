<?php

namespace Test;

use \Aruna\Webmention\DiscoverAuthor;

class DiscoverAuthorTest extends SystemTest
{
    protected function loadFixture($path)
    {
        return file_get_contents(__DIR__."/../fixtures/".$path);
    }

    /**
     * @test
     */
    public function it_returns_null_if_page_has_no_hentry()
    {
        $SUT = new DiscoverAuthor();
        $html = $this->loadFixture("authorship/no_h-entry.html");
        $event = [
            "source" => "",
            "mention_source_html" => $html
        ];
        $result = $SUT->__invoke($event);
        $this->assertNull($result["author"]);
    }

    /**
     * @test
     */
    public function it_returns_hcard_when_author_is_a_hcard()
    {
        $SUT = new DiscoverAuthor();
        $html = $this->loadFixture("authorship/h-entry_with_p-author.html");
        $event = [
            "source" => "",
            "mention_source_html" => $html
        ];
        $result = $SUT->__invoke($event);
        $expected = [
            "type" => ["h-card"],
            "properties" => [
                "name" => ["John Doe"],
                "url" => ["http://example.com/johndoe/"],
                "photo" => ["http://www.gravatar.com/avatar/fd876f8cd6a58277fc664d47ea10ad19.jpg"],
            ],
            "value" => "John Doe"
        ];
        $this->assertEquals(
            $expected,
            $result["author"]
        );
    }

    /**
     * @test
     */
    public function it_returns_hcard_when_feed_author_is_a_hcard()
    {
        $SUT = new DiscoverAuthor();
        $html = $this->loadFixture("authorship/h-feed_with_p-author.html");
        $event = [
            "source" => "",
            "mention_source_html" => $html
        ];
        $result = $SUT->__invoke($event);
        $expected = [
            "type" => ["h-card"],
            "properties" => [
                "name" => ["John Doe"],
                "url" => ["http://example.com/johndoe/"],
                "photo" => ["http://www.gravatar.com/avatar/fd876f8cd6a58277fc664d47ea10ad19.jpg"],
            ],
            "value" => "John Doe"
        ];
        $this->assertEquals(
            $expected,
            $result["author"]
        );
    }
}
