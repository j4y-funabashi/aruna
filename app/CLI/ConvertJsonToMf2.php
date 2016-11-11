<?php

namespace CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertJsonToMf2 extends Command
{

    protected function configure()
    {
        $this
            ->setName('convert')
            ->setDescription('Convert json files to mf2 format')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $this->getApplication()
            ->getService("convert_data_handler");
        $handler->handle(1000);
    }
}
