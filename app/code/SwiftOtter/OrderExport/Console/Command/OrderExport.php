<?php

namespace SwiftOtter\OrderExport\Console\Command;

use DateTime;
use Exception;
use SwiftOtter\OrderExport\Action\CollectOrderData;
use SwiftOtter\OrderExport\Model\HeaderData;
use SwiftOtter\OrderExport\Model\HeaderDataFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SwiftOtter\OrderExport\Action\ExportOrder;

class OrderExport extends Command
{
    const ARG_NAME_ORDER_ID = 'order-id';
    const OPT_NAME_SHIP_DATE = 'ship-date';
    const OPT_NAME_MERCHANT_NOTES = 'notes';

    /**
     * @param HeaderDataFactory $headerDataFactory
     * @param ExportOrder $exportOrder
     * @param ?string $name
     */
    public function __construct(
        protected HeaderDataFactory $headerDataFactory,
        protected ExportOrder  $exportOrder,
        ?string                     $name = null
    )
    {
        parent::__construct($name);
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('order-export:run')
            ->setDescription('Export order to ERP.')
            ->addArgument(
                self::ARG_NAME_ORDER_ID,
                InputArgument::REQUIRED,
                "Order id"
            )
            ->addOption(
                self::OPT_NAME_SHIP_DATE,
                'd',
                InputOption::VALUE_OPTIONAL,
                'Shipping date in format YYYY-MM-DD'
            )
            ->addOption(
                self::OPT_NAME_MERCHANT_NOTES,
                null,
                InputOption::VALUE_OPTIONAL,
                'Merchant notes'
            );
        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orderId = (int)$input->getArgument(self::ARG_NAME_ORDER_ID);
        $notes = $input->getOption(self::OPT_NAME_MERCHANT_NOTES);
        $shipDate = $input->getOption(self::OPT_NAME_SHIP_DATE);
        /** @var HeaderData $headerData */
        $headerData = $this->headerDataFactory->create();
        if ($shipDate) {
            $headerData->setShipDate(new DateTime($shipDate));
        }
        if ($notes) {
            $headerData->setMerchantNotes($notes);
        }
        $result = $this->exportOrder->execute($orderId, $headerData);
        $success = $result['success'];
        if ($success) {
            $output->writeln(__('Successfully exported order'));
        } else {
            $msg = $result['message'] ?? null;
            if ($msg === null) {
                $msg = __('Unexpected message occurred');
            }
            $output->writeln($msg);
            return 1;
        }
        return 0;
    }
}
