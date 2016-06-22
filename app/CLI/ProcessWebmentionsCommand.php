<?php

namespace CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessWebmentionsCommand
 * @author yourname
 */
class ProcessWebmentionsCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('process_webmentions')
            ->setDescription('Verify and download recieved webmentions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $this->getApplication()
            ->getService("action.process_webmentions");
        $handler->__invoke();
    }
}
