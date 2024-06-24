<?php

declare(strict_types=1);

namespace Ghost\Setup\Setup\Patch\Data;

use Exception;
use Ghost\Setup\Setup\FileManager;
use Magento\Framework\App\Config;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\ResourceModel\Website as WebsiteResourceModel;
use Magento\Store\Model\WebsiteFactory;

class ImportWebsites implements DataPatchInterface
{
    public function __construct(
        protected ModuleDataSetupInterface   $moduleDataSetup,
        protected FileManager                $fileManager,
        protected WebsiteRepositoryInterface $websiteRepository,
        protected WebsiteFactory             $websiteFactory,
        protected WebsiteResourceModel       $websiteResourceModel,
        protected Config                     $config
    )
    {
    }


    #[\Override] public static function getDependencies(): array
    {
        return [];
    }

    #[\Override] public function getAliases(): array
    {
        return [];
    }

    /**
     * @throws LocalizedException
     */
    #[\Override] public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->config->clean();
        $this->websiteRepository->clean();
        $output = $this->fileManager->getOutputStream();
        $output->writeln("<info>... Importing websites...</info>");
        $this->installWebsites(
            $this->fileManager->getParsedFixtureData('Ghost_Setup::Setup/fixtures/websites.csv')
        );
        $this->moduleDataSetup->endSetup();
    }

    /**
     * @throws AlreadyExistsException
     */
    protected function installWebsites(array $rows): void
    {
        foreach ($rows as $data) {
            $website = $this->getWebsite($data['code'], (bool)$data['is_default']);
            $website->setCode($data['code'])
                ->setName($data['name']);
            $this->websiteResourceModel->save($website);
        }
    }

    protected function getWebsite(string $code, bool $isDefault = false): WebsiteInterface
    {
        try {
            if ($isDefault) {
                return $this->websiteRepository->getDefault();
            }
            return $this->websiteRepository->get($code);
        } catch (Exception $e) {
            return $this->websiteFactory->create();
        }
    }
}
