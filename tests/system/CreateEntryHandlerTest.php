<?php

namespace Test;

use League;
use Aruna;

/**
 * Class CreateEntryHandlerTest
 */
class CreateEntryHandlerTest extends SystemTest
{

    /**
    * @test
    */
    public function it_creates_new_note_in_storage()
    {

        $adapter = new League\Flysystem\Memory\MemoryAdapter();
        $filesystem = new League\Flysystem\Filesystem($adapter);
        $noteStore = new Aruna\PostRepository($filesystem);
        $handler = new Aruna\CreateEntryHandler($noteStore);

        $entry = [
            "h" => "entry",
            "published" => "2015-01-01T01:01:01",
            "content" => "test"
        ];
        $command = new Aruna\CreateEntryCommand($entry, []);
        $newEntry = $handler->handle($command);

        $expected = '{"h":"entry","published":"2015-01-01T01:01:01+00:00","content":"test"}';
        $result = $filesystem->read($newEntry->getFilePath().".json");
        $this->assertEquals($expected, $result);
    }
}
