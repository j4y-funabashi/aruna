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
        $this
            ->setName('resize_photos')
            ->setDescription('Resize all photos in data dir');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $this->getApplication()
            ->getService("action.resize_photos");
        $handler->__invoke();
    }
}
