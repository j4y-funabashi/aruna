<?php

namespace CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResizePhotoCommand
 * @author yourname
 */
class ResizePhotoCommand extends Command
{

    protected function configure()
    {
        $this->setName('resize_photos')
            ->setDescription('Resize all photos in data dir')
            ->addOption('forever', null, InputOption::VALUE_NONE, 'Run the queue continuously')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Sleep this many seconds between queue runs', 60)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $foreverHandler = new ForeverHandler($input->getOption('forever'));
        do {

            try {
                $handler = $this->getApplication()
                    ->getService("action.resize_photos");
                $handler->__invoke();
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
