<?php

namespace CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildEventLogCommand extends Command
{

    protected function configure()
    {
        $this->setName("build-event-log")
            ->setDescription("Rebuild event log")
            ->addOption(
                "forever",
                null,
                InputOption::VALUE_NONE,
                "Run the Queue Continuously"
            )
            ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getApplication()
            ->getService("db_cache");
        $db->init();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $foreverHandler = new ForeverHandler($input->getOption('forever'));
        do {
            $app = $this->getApplication();
            try {
                $handler = $app->getService("build_event_log_handler");
                $handler->handle();
            } catch (\Exception $e) {
                $m = sprintf("Failed to run app %s", $e->getMessage());
                $app->getService('monolog')->critical($m, $e->getTrace());
            }
            if ($foreverHandler->isForever()) {
                sleep($input->getOption("sleep"));
            }
        } while ($foreverHandler->isForever());
    }
}
