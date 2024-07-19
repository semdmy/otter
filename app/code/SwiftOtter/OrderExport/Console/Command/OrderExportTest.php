<?php
declare(strict_types=1);

namespace SwiftOtter\OrderExport\Console\Command;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\SearchResultInterface;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails\Collection as OrderExportDetailsCollection;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails\CollectionFactory as OrderExportDetailsCollectionFactory;

class OrderExportTest extends Command
{
    public function __construct(
        protected OrderExportDetailsCollectionFactory $orderExportDetailsCollectionFactory,
        protected OrderExportDetailsRepositoryInterface $orderExportDetailsRepository,
        protected SearchCriteriaBuilder $searchCriteriaBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->orderExportDetailsCollectionFactory = $orderExportDetailsCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('order-export:test')
            ->setDescription('Test various order export features');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        /** @var OrderExportDetailsCollection $exportDetailsCollection */
//        $exportDetailsCollection = $this->orderExportDetailsCollectionFactory->create();
//        $output->writeln(print_r($exportDetailsCollection->getSelect()->__toString(), true));




//        $exportDetailsCollection->join(
//            ['sales_order' => $exportDetailsCollection->getTable('sales_order')],
//            'sales_order.entity_id=main_table.id',
//            ['main_table.ship_on', 'sales_order.base_subtotal_incl_tax']
//        );
//        $output->writeln(print_r($exportDetailsCollection->getSelect()->__toString(), true));
//        foreach ($exportDetailsCollection as $exportDetails) {
//            $output->writeln(print_r($exportDetails->getData(), true));
//        }

        $this->searchCriteriaBuilder->addFilter('order_id', 3);
        $exportDetails = $this->orderExportDetailsRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($exportDetails as $exportDetail) {
            $output->writeln(print_r($exportDetail->getData(), true));
        }

        return 0;
    }
}
