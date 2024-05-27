<?php declare(strict_types=1);

namespace Ghost\CliCommand\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class  CustomLogFile extends SymfonyCommand
{
    public function __construct(
        protected string $name,
        protected LoggerInterface $logger
    ) { 
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('log:custom');
        $this->setDescription('Log to custom file.');

        parent::configure();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info("Tra-la-la");
        $output->writeln('hello!');

        return 0;
    }
}