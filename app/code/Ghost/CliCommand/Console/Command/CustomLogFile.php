<?php declare(strict_types=1);

namespace Ghost\CliCommand\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\MessageQueue\Publisher;
use Psr\Log\LoggerInterface;

class  CustomLogFile extends SymfonyCommand
{
    private const TOPIC_NAME = "queue.order.create.db";
    private const TOPIC_NAME_AMQP = "queue.order.create.rabbit";

    public function __construct(
        protected string $name,
        protected LoggerInterface $logger,
        protected Publisher $publisher
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
        $this->publisher->publish(self::TOPIC_NAME, "dhello from b");
        $output->writeln('published db');
        $this->publisher->publish(self::TOPIC_NAME_AMQP, "hello from amqp!!!");
        $output->writeln('published amqp');
        return 0;
    }
}