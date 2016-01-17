<?php

namespace CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use League;
use Aruna;

/**
 * Class CreateEntryCommand
 * @author yourname
 */
class CreateEntryCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('create')
            ->setDescription('Create a new post')
            ->addArgument(
                'h',
                InputArgument::REQUIRED,
                'used to specify the object type being created'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adapter = new League\Flysystem\Memory\MemoryAdapter();
        $filesystem = new League\Flysystem\Filesystem($adapter);
        $noteStore = new Aruna\EntryRepository($filesystem);
        $handler = new Aruna\CreateEntryHandler($noteStore);

        $entry = [
            "h" => $input->getArgument("h"),
            "published" => "2015-01-01T01:01:01",
            "content" => "test"
        ];
        $command = new Aruna\CreateEntryCommand($entry);
        $newEntry = $handler->handle($command);

        $output->writeln($newEntry->asJson());
    }
}
