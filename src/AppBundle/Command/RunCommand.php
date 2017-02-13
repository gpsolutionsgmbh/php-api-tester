<?php
// src/AppBundle/Command/RunCommand.php
namespace AppBundle\Command;

use AppBundle\Model\Container;
use AppBundle\Model\TestRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunCommand
 * @package AppBundle\Command
 */
class RunCommand extends Command
{

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:run')
            // the short description shown while running "php bin/console list"
            ->setDescription('Test API')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to run API tests...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $output->writeln([
            'Start testing',
            '============',
            '',
        ]);
        $testRunner = new TestRunner();
        $testRunner->run();
        $runTime = microtime(true) - $startTime;
        $output = Container::getInstance()->getContainer()->get('output');
        $output->writeln([
            '',
            '============',
            'End testing',
            "Run time: $runTime s"
        ]);
    }
}