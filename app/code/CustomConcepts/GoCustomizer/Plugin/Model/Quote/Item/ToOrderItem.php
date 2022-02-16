<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Plugin\Model\Quote\Item;

class ToOrderItem {

    /**
     *
     * @var type \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     *
     * @var type \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     *
     * @var type \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     *
     * @var type \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected $goCustomizerClient;

    protected $logger;

    /**
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \CustomConcepts\GoCustomizer\Helper\Client $goCustomizerClient,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
        $this->storeManager = $storeManager;
        $this->goCustomizerClient = $goCustomizerClient;
        $this->logger = $logger;
    }

    /**
     *
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \CustomConcepts\GoCustomizer\Plugin\Model\Quote\Item\callable $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param type $additional
     * @return type
     */
    public function aroundConvert(
    \Magento\Quote\Model\Quote\Item\ToOrderItem $subject, callable $proceed, \Magento\Quote\Model\Quote\Item\AbstractItem $item, $additional = []
    ) {
        $orderItem = $proceed($item, $additional);
        $gocustomizerdata = unserialize($item->getGocustomizerData());

        /** get mediaDir */
        $mediaDir = $this->directoryList->getPath('media') . '/';

        /** @BEGIN get final design */
        if(!empty($gocustomizerdata['session']) && !empty($gocustomizerdata['customer_auth']) && empty($gocustomizerdata['design'])){
            $response = $this->goCustomizerClient->saveFinalDesign($gocustomizerdata['customer_auth'], $gocustomizerdata['session']);

            if(!empty($response['error'])){
                $this->logger->info('Something went wrong? With Observer call to customizer for auth:'.$gocustomizerdata['customer_auth'] .', session:' . $gocustomizerdata['session']."\n".$response['error']."\n".'Order-item:'.$orderItem->getId());
            }

            if(!empty($response['data'])){
                $design_data = $this->fileDriver->fileGetContents($response['data']);
                $path = 'customer_data/orders/' . date("Ymd") . '/';
                $finaldesign_path = $path . basename($response['data']);

                /** check and create folder if exists */
                $this->file->checkAndCreateFolder($mediaDir . $path);
                /** @file_put_content */
                $this->fileDriver->filePutContents($mediaDir . $finaldesign_path, $design_data, 0777);

                $gocustomizerdata['design'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . $finaldesign_path;
                $orderItem->setHiresFinalPng(date("Ymd") . '/' . basename($finaldesign_path));

                if(isset($response['design_engine'])){
                    $gocustomizerdata['design_engine_url'] = $response['design_engine'];
                }

                $this->logger->info('Order-item:'.$orderItem->getId().' customer_design:'.print_r($finaldesign_path, true));
            }
        } else {
            $this->logger->info('Quote-item:'.$orderItem->getId().' has no customer_design.');
        }
        /** @END get final design */

        $orderItem->setGocustomizerData(serialize($gocustomizerdata));

        return $orderItem;
    }

}
