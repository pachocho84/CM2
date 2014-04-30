<?php

namespace CM\CMBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Application;
use CM\CMBundle\Console\Command\MoveFilesCommand;

class CMBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $application->add(new MoveFilesCommand);
    }
}
