<?php

namespace CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCacheCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('cache')
            ->setDescription('Generate read cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $this->getApplication()
            ->getService("process_cache_handler");
        $handler->handle();
    }
}
