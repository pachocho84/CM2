<?php

namespace CM\CMBundle\Console\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveFilesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cm:files:remove')
            ->setDescription('Deletes files in the public folder.');
    }

    private function rrmdir($dir, $first = false)
    { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object, true); else unlink($dir."/".$object); 
                } 
            } 
            reset($objects); 
            if ($first) rmdir($dir); 
        } 
    } 

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $imagesDir = getcwd().'/src/CM/CMBundle/Resources/public'.$this->getContainer()->getParameter('images.dir');
        $audioDir = getcwd().'/src/CM/CMBundle/Resources/public'.$this->getContainer()->getParameter('audio.dir');
        $cssDir = $this->getContainer()->getParameter('web.abs_dir').'/css';
        $jsDir = $this->getContainer()->getParameter('web.abs_dir').'/js';

        $output->writeln('Removing images directory...');
        if (file_exists($imagesDir)) {
            $this->rrmdir($imagesDir);
        }

        $output->writeln('Removing audio directory...');
        if (file_exists($audioDir)) {
            $this->rrmdir($audioDir);
        }

        $output->writeln('Removing css...'.$cssDir);
        if (file_exists($cssDir)) {
            $this->rrmdir($cssDir);
        }

        $output->writeln('Removing js...'.$jsDir);
        if (file_exists($jsDir)) {
            $this->rrmdir($jsDir);
        }
    }
}