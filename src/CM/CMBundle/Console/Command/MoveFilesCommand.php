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
        $imagesDir = getcwd().'/src/CM/CMBundle/Resources/public'.$this->getContainer()->getParameter('images_full.dir');
        $audioDir = getcwd().'/src/CM/CMBundle/Resources/public'.$this->getContainer()->getParameter('audio.dir');
        $filesDir = getcwd().'/src/CM/CMBundle/Resources/test_files';

        $output->writeln('Moving images...');
        if (!file_exists($imagesDir)) {
            mkdir($imagesDir, 0777, true);
        }
        $files = scandir($filesDir.'/images');
        foreach ($files as $file) {
            if (!is_file($filesDir.'/images'.'/'.$file)) continue;
            copy($filesDir.'/images'.'/'.$file, $imagesDir.'/'.$file);
            $output->writeln($file.' -> '.$imagesDir.'/'.$file);
        }

        $output->writeln('Moving audio files...');
        if (!file_exists($audioDir)) {
            mkdir($audioDir, 0777, true);
        }
        $files = scandir($filesDir.'/audio');
        foreach ($files as $file) {
            if (!is_file($filesDir.'/audio'.'/'.$file)) continue;
            copy($filesDir.'/audio'.'/'.$file, $audioDir.'/'.$file);
            $output->writeln($file.' -> '.$audioDir.'/'.$file);
        }

    }
}