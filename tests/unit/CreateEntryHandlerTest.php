<?php

namespace Test;

use Aruna;

/**
 * Class CreateEntryHandlerTest
 * @author yourname
 */
class CreateEntryHandlerTest extends UnitTest
{

    /**
     * @test
     */
    public function it_creates_a_new_entry_and_passes_it_to_notestore()
    {
        $noteStore = $this->prophesize("\Aruna\EntryRepository");
        $SUT = new Aruna\CreateEntryHandler($noteStore->reveal());

        $entry = [
            "h" => "entry",
            "published" => "2015-01-01T01:01:01",
            "content" => "test"
        ];
        $command = new Aruna\CreateEntryCommand($entry);

        $newEntry = $SUT->handle($command);

        $noteStore->save(new Aruna\Entry($entry))
            ->shouldBeCalled();
    }
}
