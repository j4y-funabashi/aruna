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
            ->setDescription('Generate read cache')
            ->addOption('forever', null, InputOption::VALUE_NONE, 'Run the queue continuously')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Sleep this many seconds between queue runs', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $foreverHandler = new ForeverHandler($input->getOption('forever'));
        do {
            $app = $this->getApplication();
            try {
                $handler = $app->getService("process_cache_handler");
                $handler->handle();
            } catch (\Exception $e) {
                $m = sprintf("Failed to run app %s", $e->getMessage());
                $app->getService('monolog')->critical($m);
            }
            sleep($input->getOption("sleep"));
        } while ($foreverHandler->isForever());
    }
}
