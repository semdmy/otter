<?php

namespace Ghost\CliCommand\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogCmd extends Command
{
    public const string NAME = 'name';

    protected function configure(): void
    {
        $options = [
            new InputOption(
                self::NAME,
                null,
                InputOption::VALUE_REQUIRED,
                'Name'
            )
        ];

        $this->setName('catalog:cmd');
        $this->setDescription('Demo catalog command line');
        $this->setDefinition($options);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sdiuhgisufhgiusfhgiuhsfiughusifhgiusfhgiuhdsfuighiudfshgiudhfiughdiufhgiudfhguihdfughdiufhgiudfhighdfiug = 9;
        if ($name = $input->getOption(self::NAME)) {
            $output->writeln("Hello " . $name);
        } else {
            $output->writeln("Hello World");
        }
        return 0;
    }
}
