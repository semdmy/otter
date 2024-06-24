<?php

declare(strict_types=1);

namespace Ghost\Setup\Setup\Patch\Data;

use Exception;
use Ghost\Setup\Setup\FileManager;
use Ghost\Setup\Setup\DataHelper;
use Magento\Framework\App\Config;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResourceModel;
use Magento\Store\Model\ResourceModel\Store as StoreResourceModel;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\Theme\Registration;
use Magento\Theme\Setup\Patch\Data\RegisterThemes;

class ImportStores implements DataPatchInterface
{
    public function __construct(
        protected ModuleDataSetupInterface   $moduleDataSetup,
        protected FileManager                $fileManager,
        protected DataHelper                 $dataHelper,
        protected StoreManagerInterface      $storeManager,
        protected WebsiteRepositoryInterface $websiteRepository,
        protected GroupFactory               $groupFactory,
        protected StoreRepositoryInterface   $storeRepository,
        protected StoreFactory               $storeFactory,
        protected StoreResourceModel         $storeResourceModel,
        protected GroupResourceModel         $groupResourceModel,
        protected Registration               $themeRegistration,
        protected GroupRepositoryInterface   $groupRepository,
        protected Config                     $config
    )
    {
    }

    #[\Override] public static function getDependencies(): array
    {
        return [
            ImportWebsites::class,
            ImportGroups::class,
            RegisterThemes::class
        ];
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
        $this->groupRepository->clean();
        $this->storeRepository->clean();
        $output = $this->fileManager->getOutputStream();
        $output->writeln("<info>... Importing store views...</info>");
        $this->installStores(
            $this->fileManager->getParsedFixtureData('Ghost_Setup::Setup/fixtures/stores.csv')
        );
        $this->moduleDataSetup->endSetup();
    }

    /**
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     */
    public function installStores(array $rows): void
    {
        //Register all themes before applying to stores
        $this->themeRegistration->register();
        foreach ($rows as $data) {
            $group = $this->getGroup($data['group']);
            $store = $this->getStore($data['code'], (bool)$data['is_default']);
            $store->setCode($data['code'])
                ->setName($data['name'])
                ->setIsActive(1)
                ->setStoreGroupId($group->getId())
                ->setWebsiteId($group->getWebsiteId());
            $this->storeResourceModel->save($store);
            $this->saveStoreLocale($store, $data);
        }
        $this->storeManager->reinitStores();
    }

    protected function getStore(string $code, bool $isDefault = false): StoreInterface
    {
        try {
            if ($isDefault) {
                $websiteDefault = $this->websiteRepository->getDefault();
                /** @var Group $defaultGroup */
                $defaultGroup = $this->groupRepository->get($websiteDefault->getDefaultGroupId());
                return $defaultGroup->getDefaultStore();
            }
            return $this->storeRepository->get($code);
        } catch (Exception $e) {
            return $this->storeFactory->create();
        }
    }

    /**
     * @throws NoSuchEntityException
     */
    protected function getGroup(string $code): Group
    {
        $group = $this->groupFactory->create();
        $this->groupResourceModel->load($group, $code, 'code');
        if (!$group->getId()) {
            throw new NoSuchEntityException(__('No such group entity with code "%1"', $code));
        }
        return $group;
    }

    protected function saveStoreLocale(StoreInterface $store, array $data): void
    {
        $this->dataHelper->initStoreLocale((int)$store->getId(), $data['locale']);
    }
}
