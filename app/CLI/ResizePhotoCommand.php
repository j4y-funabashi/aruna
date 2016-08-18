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
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Sleep this many seconds between queue runs', 10)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $foreverHandler = new ForeverHandler($input->getOption('forever'));
        do {
            $handler = $this->getApplication()
                ->getService("action.resize_photos");
            $handler->__invoke();
            sleep($input->getOption("sleep"));
        } while ($foreverHandler->isForever());
    }
}
