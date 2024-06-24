<?php

declare(strict_types=1);

namespace Ghost\Setup\Setup\Patch\Data;

use Exception;
use Ghost\Setup\Setup\FileManager;
use Magento\Framework\App\Config;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResourceModel;

class ImportGroups implements DataPatchInterface
{
    public function __construct(
        protected ModuleDataSetupInterface   $moduleDataSetup,
        protected FileManager                $fileManager,
        protected GroupFactory               $groupFactory,
        protected WebsiteRepositoryInterface $websiteRepository,
        protected GroupResourceModel         $groupResourceModel,
        protected GroupRepositoryInterface   $groupRepository,
        protected Config                     $config
    )
    {
    }

    #[\Override] public static function getDependencies()
    {
        return [ImportWebsites::class];
    }

    #[\Override] public function getAliases()
    {
        return [];
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    #[\Override] public function apply(): void
    {
        $this->moduleDataSetup->startSetup();
        $this->config->clean();
        $this->websiteRepository->clean();
        $this->groupRepository->clean();
        $output = $this->fileManager->getOutputStream();
        $output->writeln("<info>... Importing stores...</info>");
        $this->installGroups(
            $this->fileManager->getParsedFixtureData('Ghost_Setup::Setup/fixtures/groups.csv')
        );
        $this->moduleDataSetup->endSetup();
    }

    /**
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     */
    public function installGroups(array $rows): void
    {
        $defaultGroupId = $this->websiteRepository->getDefault()->getDefaultGroupId();
        $defaultGroup = $this->groupRepository->get($defaultGroupId);
        foreach ($rows as $data) {
            $website = $this->websiteRepository->get($data['website']);
            $group = $this->getGroup($data['code'], (bool)$data['is_default']);
            $group->setCode($data['code'])
                ->setName($data['name'])
                ->setRootCategoryId($defaultGroup->getRootCategoryId())
                ->setWebsite($website);
            $this->groupResourceModel->save($group);
        }
    }

    protected function getGroup(string $code, bool $isDefault = false): GroupInterface
    {
        try {
            if ($isDefault) {
                $websiteDefault = $this->websiteRepository->getDefault();
                $groupId = $websiteDefault->getDefaultGroupId();
                return $this->groupRepository->get($groupId);
            }
            $group = $this->groupFactory->create();
            $this->groupResourceModel->load($group, $code, 'code');
            return $group;
        } catch (Exception $e) {
            return $this->groupFactory->create();
        }
    }
}
