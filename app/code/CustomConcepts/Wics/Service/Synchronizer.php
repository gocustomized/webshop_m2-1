<?php
declare(strict_types=1);

namespace CustomConcepts\Wics\Service;

use CustomConcepts\Wics\Api\Data\WicsItemResponseInterface;
use CustomConcepts\Wics\Helper\Config;
use CustomConcepts\Wics\Logger\Logger;
use CustomConcepts\Wics\Model\WicsAdapter;
use CustomConcepts\Wics\Model\Sync;
use CustomConcepts\Wics\Model\SyncFactory;
use CustomConcepts\Wics\Model\SyncManagement;
use CustomConcepts\Wics\Model\SyncRepository;

class Synchronizer
{
    /**
     * @var WicsAdapter
     */
    private $adapter;

    /**
     * @var SyncRepository
     */
    private $syncRepository;

    /**
     * @var SyncManagement
     */
    private $syncManagement;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var WicsItemUpdater
     */
    private $wicsItemUpdater;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SyncFactory
     */
    private $syncFactory;

    /**
     * @param WicsAdapter $adapter
     * @param SyncRepository $syncRepository
     * @param SyncManagement $syncManagement
     * @param Logger $logger
     * @param WicsItemUpdater $wicsItemUpdater
     * @param Config $config
     * @param SyncFactory $syncFactory
     */
    public function __construct(
        WicsAdapter $adapter,
        SyncRepository $syncRepository,
        SyncManagement $syncManagement,
        Logger $logger,
        WicsItemUpdater $wicsItemUpdater,
        Config $config,
        SyncFactory $syncFactory
    ) {
        $this->adapter = $adapter;
        $this->syncRepository = $syncRepository;
        $this->syncManagement = $syncManagement;
        $this->logger = $logger;
        $this->wicsItemUpdater = $wicsItemUpdater;
        $this->config = $config;
        $this->syncFactory = $syncFactory;
    }

    public function synchronize() : void
    {
        $this->logger->notice('Start synchronization');
        if (!$this->config->isWicsIntegrationEnabled()) {
            $this->logger->notice('Wics Integration disabled.');

            return;
        }
        $sync = $this->syncFactory->create();
        $sync->setStatus('started');
        $this->syncRepository->save($sync);

        $wicsResponseItems = $this->adapter->getWicsItems();
        if ($wicsResponseItems->isResponseEmpty()) {
            $this->saveSkippedSync($sync, 'Wics response is empty, synchronization skipped');
        }
        if ($this->areItemsNotEqual($this->syncManagement->getLatestSuccessful(), $wicsResponseItems)) {
            try {
                $this->wicsItemUpdater->update($wicsResponseItems);
                // save success sync with items
                $sync->setStatus('successful');
                $sync->setResponseHash($wicsResponseItems->getResponseHash());
                $this->syncRepository->save($sync);
            } catch (\Exception $exception) {
                // save failed sync, with error msg
                $sync->setStatus('failed');
                $sync->setMessage($exception->getMessage());
                $this->syncRepository->save($sync);
                return;
            }
        } else {
            $this->saveSkippedSync($sync, 'WICS response the same, synchronization skipped');
        }
    }

    /**
     * @param Sync $sync
     * @param string $msg
     */
    private function saveSkippedSync(Sync $sync, string $msg)
    {
        $sync->setStatus('skipped');
        $sync->setMessage($msg);
        $this->syncRepository->save($sync);
        $this->logger->notice($msg);
    }

    /**
     * @param WicsItemResponseInterface | bool $oldItems
     * @param WicsItemResponseInterface $newItems
     * @return bool
     */
    private function areItemsNotEqual($oldItems, WicsItemResponseInterface $newItems)
    {
        if (!$oldItems) {
            return true;
        }

        return $oldItems->getResponseHash() !== $newItems->getResponseHash();
    }
}
