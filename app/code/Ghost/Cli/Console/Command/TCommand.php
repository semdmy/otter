<?php declare(strict_types=1);

namespace Ghost\Cli\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TCommand extends SymfonyCommand
{
    private const NAME = 'name';

    protected function configure(): void
    {
        $this->setName('temp:command');
        $this->setDescription('Temporary cli command.');
        $this->addOption(
            self::NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Name'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exitCode = 0;

        if ($name = $input->getOption(self::NAME)) {
            $output->writeln('<info>Provided name is ' . $name . '</info>');
        }
        return $exitCode;
    }
}
