<?php

/**
 * CustomConcepts_GoCustomizer extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_GoCustomizer
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\GoCustomizer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PlaceOrderSuccess implements ObserverInterface {

    protected $directoryList;

    protected $file;

    protected $fileDriver;

    protected $storeManager;

    protected $goCustomizerClient;

    protected $logger;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \CustomConcepts\GoCustomizer\Helper\Client $goCustomizerClient,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->fileDriver = $fileDriver;
        $this->storeManager = $storeManager;
        $this->goCustomizerClient = $goCustomizerClient;
        $this->logger = $logger;
    }

    public function execute(Observer $observer){
        $quote = $observer->getQuote();
        $order = $observer->getOrder();

        try {
            // Map Quote Item with Quote Item Id
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                $quoteItems[$quoteItem->getId()] = $quoteItem;
            }

            //set final design
            foreach ($order->getAllVisibleItems() as $orderItem) {
                $quoteItemId = $orderItem->getQuoteItemId();
                if(!isset($quoteItems[$quoteItemId])){
                    $this->logger->info('Cannot find Quote Item for Order Item ' . $orderItem->getId());
                    continue;
                }

                $gocustomizerdata = unserialize($quoteItems[$quoteItemId]->getGocustomizerData());

                if(!empty($gocustomizerdata['session']) && !empty($gocustomizerdata['customer_auth']) && empty($gocustomizerdata['design'])){
                    $response = $this->goCustomizerClient->saveFinalDesign($gocustomizerdata['customer_auth'], $gocustomizerdata['session']);
                    $mediaDir = $this->directoryList->getPath('media') . '/';

                    if(!empty($response['error'])){
                        $this->logger->info('Something went wrong? With Observer call to customizer for auth:'.$gocustomizerdata['customer_auth'] .', session:' . $gocustomizerdata['session']."\n".$response['error']."\n".'Order-item:'.$orderItem->getId());
                        continue;
                    }

//                    if(!empty($response['data'])){
//                        $design_data = $this->fileDriver->fileGetContents($response['data']);
//                        $path = 'customer_data/orders/' . date("Ymd") . '/';
//                        $finaldesign_path = $path . basename($response['data']);
//
//                        /** check and create folder if exists */
//                        $this->file->checkAndCreateFolder($mediaDir . $path);
//                        /** @file_put_content */
//                        $this->fileDriver->filePutContents($mediaDir . $finaldesign_path, $design_data, 0777);
//
//                        $gocustomizerdata['design'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . $finaldesign_path;
//                        $orderItem->setHiresFinalPng(date("Ymd") . '/' . basename($finaldesign_path));
//
//                        if(isset($response['design_engine'])){
//                            $gocustomizerdata['design_engine_url'] = $response['design_engine'];
//                        }
//
//                        $this->logger->info('Order-item:'.$orderItem->getId().' customer_design:'.print_r($finaldesign_path, true));
//                    }
                    $gocustomizerdata['design'] = $gocustomizerdata['thumb']; //just use thumb data so we no longer need to save the image on magento2 media
                    if(isset($response['design_engine'])){
                        $gocustomizerdata['design_engine_url'] = $response['design_engine'];
                        $this->logger->info('Order-item:'.$orderItem->getId().' customer_design:'.$response['design_engine']);
                    }
                } else {
                    $this->logger->info('Quote-item:'.$orderItem->getId().' has no customer_design.');
                }

                $orderItem->setGocustomizerData(serialize($gocustomizerdata));
            }
        } catch (\Exception $e) {
            $errorPrefix = 'CustomConcepts_GoCustomizer:PlaceOrderSuccess:execute - ';
            $this->logger->error($errorPrefix . $e->getMessage());
        }
    }
}
