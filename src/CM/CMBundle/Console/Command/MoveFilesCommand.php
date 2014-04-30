<?php

namespace CM\CMBundle\Console\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MoveFilesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cm:files:move')
            ->setDescription('Move files (images & audio) in the correct location.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $images = getcwd().$this->getContainer()->getParameter('images_full.dir');
        $audio = getcwd().$this->getContainer()->getParameter('audio.dir');
        $files = array_keys($this->getContainer()->getParameter('liip_imagine.filter_sets'));

        $output->writeln('Moving images...');
        foreach ($files as $file) {
            $output->writeln('')
        }

    }
}