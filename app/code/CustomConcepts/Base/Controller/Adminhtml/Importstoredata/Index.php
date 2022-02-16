<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CustomConcepts\Base\Controller\Adminhtml\Importstoredata;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }
    
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('CustomConcepts_Base::config_gocustomized_copydata');
    }
    
    public function execute() {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $from_store = $this->getConfigValue('copydata/general/from_store');
        $to_store = $this->getConfigValue('copydata/general/to_store');
        $type = $this->getConfigValue('copydata/general/type');
        if ($from_store > 0 && $to_store > 0 && !($from_store == $to_store)) {
            switch ($type) {
                case "all":
                    $this->copyStoreConfigs($to_store, $from_store);
                    $this->copyCmsBlocks($to_store, $from_store);
                    $this->copyCmsPages($to_store, $from_store);
                    $this->copyCategories($to_store, $from_store);
                    $this->copyAttributes($to_store, $from_store);
                    $this->copyProducts($to_store, $from_store);
                    break;
                case "configs":
                    $this->copyStoreConfigs($to_store, $from_store);
                    break;
                case "cms_blocks":
                    $this->copyCmsBlocks($to_store, $from_store);
                    break;
                case "cms_pages":
                    $this->copyCmsPages($to_store, $from_store);
                    break;
                case "categories":
                    $this->copyCategories($to_store, $from_store);
                    break;
                case "attributes":
                    $this->copyAttributes($to_store, $from_store);
                    break;
                case "products":
                    $this->copyProducts($to_store, $from_store);
                    break;
            }
            $this->messageManager->addSuccess(__('Kindly refresh reindex management.'));
        } elseif ($from_store == $to_store) {
            $this->messageManager->addError(__('Please select different stores or save config first to import data.'));
        } else {
            $this->messageManager->addError(__('Please select stores.'));
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
        die;
    }

    public function getConfigValue($path) {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function copyStoreConfigs($to_store, $from_store) {
        
        /** Store Configurations */
        $write = $this->resourceConnection->getConnection('core_write');
        $table = $this->resourceConnection->getTableName('core_config_data');
        $write->query("DELETE FROM  $table
    			WHERE scope = 'stores'
    			AND scope_id = $to_store");

        $write->query("INSERT INTO  $table (
    			scope,
    			scope_id,
    			path,
    			value
    	) SELECT
    			scope,
    			$to_store,
    			path,
    			value
    			FROM  core_config_data
    			WHERE scope = 'stores'
    			AND scope_id = $from_store;
    			");

        $this->messageManager->addSuccess(__('store configurations successfully copied.'));
    }

    public function copyCmsBlocks($to_store, $from_store) {

        $write = $this->resourceConnection->getConnection('core_write');

        /** cms_block_store */
        $table = $this->resourceConnection->getTableName('cms_block_store');
        $write->query("DELETE FROM  $table WHERE store_id = $to_store");
        $write->query("
		    	INSERT INTO  $table (
		    			block_id,
		    			store_id
		    	) SELECT
		    			block_id,
		    			$to_store
		    			FROM  $table
		    			WHERE store_id = $from_store");

        /* copy banner images */

        /* $write->query("DELETE FROM  buyshopflex
          WHERE store_id = $to_store AND store_id NOT LIKE '%,%'");

          $write->query('INSERT INTO  buyshopflex (
          image,
          link,
          status,
          store_id,
          sort_order
          ) SELECT
          image,
          link,
          status,
          ' . $to_store . ',
          sort_order
          FROM  buyshopflex
          WHERE store_id LIKE \'%' . $from_store . '%\'
          '); */

        /* copy banner images */

        /* $write->query("DELETE FROM  prodfaqs_store
          WHERE store_id = $to_store");

          $write->query("INSERT INTO  prodfaqs_store (
          topic_id,
          store_id
          ) SELECT
          topic_id,
          $to_store
          FROM  prodfaqs_store
          WHERE store_id = $from_store
          "); */

        $this->messageManager->addSuccess(__('CMS Blocks successfully copied.'));
    }

    public function copyCmsPages($to_store, $from_store) {
        /** cms_page_store */
        $write = $this->resourceConnection->getConnection('core_write');
        $table = $this->resourceConnection->getTableName('cms_page_store');
        $write->query("
    			DELETE FROM  $table
    			WHERE store_id = $to_store");

        $write->query("INSERT INTO  $table (
    			page_id,
    			store_id
    	) SELECT
    			page_id,
    			$to_store
    			FROM  $table
    			WHERE store_id = $from_store
    			");
        $this->messageManager->addSuccess(__('CMS Pages successfully copied.'));
    }

    public function copyCategories($to_store, $from_store) {
        $write = $this->resourceConnection->getConnection('core_write');
        $table = $this->resourceConnection->getTableName('catalog_category_entity_int');
        /** catalog_category_entity_int */
        $write->query("DELETE FROM  $table
    					WHERE store_id = $to_store");

        $write->query("INSERT INTO  $table (
    					attribute_id,
    					store_id,
    					entity_id,
    					value
    			) SELECT
    					attribute_id,
    					$to_store,
    					entity_id,
    					value
    					FROM  $table
    					WHERE store_id = $from_store");
        $txttable = $this->resourceConnection->getTableName('catalog_category_entity_text');
        
        /** catalog_category_entity_text */
        $write->query("DELETE FROM  $txttable
    					WHERE store_id = $to_store");

        $write->query("INSERT INTO  $txttable (
    					attribute_id,
    					store_id,
    					entity_id,
    					value
    			) SELECT
    					attribute_id,
    					$to_store,
    					entity_id,
    					value
    					FROM  $txttable
    					WHERE store_id = $from_store");

        /** catalog_category_entity_varchar */
        $varchartable = $this->resourceConnection->getTableName('catalog_category_entity_varchar');
        $write->query("DELETE FROM  $varchartable
    					WHERE store_id = $to_store");

        $write->query("INSERT INTO  $varchartable (
    					attribute_id,
    					store_id,
    					entity_id,
    					value
    			) SELECT
    					attribute_id,
    					$to_store,
    					entity_id,
    					value
    					FROM  $varchartable
    					WHERE store_id = $from_store;
    					");
        
        
        /** catalog_category_entity_decimal */
        $dectable = $this->resourceConnection->getTableName('catalog_category_entity_decimal');
        $write->query("DELETE FROM  $dectable
    					WHERE store_id = $to_store");
        
        $write->query("INSERT INTO  $dectable (
    					attribute_id,
    					store_id,
    					entity_id,
    					value
    			) SELECT
    					attribute_id,
    					$to_store,
    					entity_id,
    					value
    					FROM  $dectable
    					WHERE store_id = $from_store;
    					");
         
         
         /** catalog_category_entity_datetime */
        $dttable = $this->resourceConnection->getTableName('catalog_category_entity_datetime');
        $write->query("DELETE FROM  $dttable
    					WHERE store_id = $to_store");
        
        $write->query("INSERT INTO  $dttable (
    					attribute_id,
    					store_id,
    					entity_id,
    					value
    			) SELECT
    					attribute_id,
    					$to_store,
    					entity_id,
    					value
    					FROM  $dttable
    					WHERE store_id = $from_store;
    					");
        
        
        $this->messageManager->addSuccess(__('Categories successfully copied.'));
    }

    public function copyAttributes($to_store, $from_store) {
        $write = $this->resourceConnection->getConnection('core_write');
        $evlbltable = $this->resourceConnection->getTableName('eav_attribute_label');
        $write->query("DELETE FROM $evlbltable
    			WHERE store_id = $to_store");

        $write->query("INSERT INTO $evlbltable (
    			attribute_id,
    			store_id,
    			value
    	) SELECT
    			attribute_id,
    			$to_store,
    			value
    			FROM $evlbltable
    			WHERE store_id = $from_store");

        /** eav_attribute_option_value */
        $eavvaltable = $this->resourceConnection->getTableName('eav_attribute_option_value');
        $write->query("DELETE FROM $eavvaltable
    			WHERE store_id = $to_store");

        $write->query("INSERT INTO $eavvaltable (
    			option_id,
    			store_id,
    			value
    				) SELECT
    			option_id,
    			$to_store,
    			value
    			FROM $eavvaltable
    			WHERE store_id = $from_store;
    			");

        /** eav_attribute_option_swatch */
        $eavswtachtable = $this->resourceConnection->getTableName('eav_attribute_option_swatch');
        $write->query("DELETE FROM $eavswtachtable
    			WHERE store_id = $to_store");

        $write->query("INSERT INTO $eavswtachtable (
    			option_id,
    			store_id,
                        type,
    			value
    				) SELECT
    			option_id,
    			$to_store,
                        type,
    			value
    			FROM $eavswtachtable
    			WHERE store_id = $from_store;
    			");

        $this->messageManager->addSuccess(__('Attributes successfully copied.'));
    }

    public function copyProducts($to_store, $from_store) {
        
        $write = $this->resourceConnection->getConnection('core_write');
        $table = $this->resourceConnection->getTableName('catalog_product_entity_datetime');
        
        /** catalog_product_entity */
        # PROCESS datetime VALUES
        $write->query("DELETE FROM $table
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $table (
    			store_id,
    			attribute_id,
    			entity_id,
    			value
    	) SELECT
    			$to_store,
    			attribute_id,
    			entity_id,
    			value
    			FROM $table
    			WHERE store_id = $from_store");


        # PROCESS decimal VALUES
        $dttable = $this->resourceConnection->getTableName('catalog_product_entity_datetime');
        $write->query("DELETE FROM $dttable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $dttable (
    	store_id,
    	attribute_id,
    	entity_id,
    	value
    	) SELECT
    	$to_store,
    	attribute_id,
    	entity_id,
    	value
    	FROM $dttable
    	WHERE store_id = $from_store");


        # PROCESS gallery VALUES
        $gallerytable = $this->resourceConnection->getTableName('catalog_product_entity_gallery');
        $write->query("DELETE FROM $gallerytable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $gallerytable (
    	`attribute_id`, 
        `store_id`, 
        `entity_id`, 
        `position`, 
        `value`
    	) SELECT    	
    	attribute_id,
        $to_store,
    	entity_id,
    	position,
    	value
    	FROM $gallerytable
    	WHERE store_id = $from_store");

        # PROCESS int VALUES
        $inttable = $this->resourceConnection->getTableName('catalog_product_entity_int');
        $write->query("DELETE FROM $inttable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $inttable (
    	attribute_id,
    	store_id,
    	entity_id,
    	value
    	) SELECT
    	attribute_id,
    	$to_store,
    	entity_id,
    	value
    	FROM $inttable
    	WHERE store_id = $from_store");

        # PROCESS text VALUES
        $txttable = $this->resourceConnection->getTableName('catalog_product_entity_text');
        $write->query("DELETE FROM $txttable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $txttable (
    	store_id,
    	attribute_id,
    	entity_id,
    	value
    	) SELECT
    	$to_store,
    	attribute_id,
    	entity_id,
    	value
    	FROM $txttable
    	WHERE store_id = $from_store");

        # PROCESS varchar VALUES
        $varchartable = $this->resourceConnection->getTableName('catalog_product_entity_varchar');
        $write->query("DELETE FROM $varchartable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $varchartable (
    			store_id,
    			attribute_id,
    			entity_id,
    			value
    	) SELECT
    			$to_store,
    			attribute_id,
    			entity_id,
    			value
    			FROM $varchartable
    			WHERE store_id = $from_store");


        /** catalog_product_entity_media_gallery */
        # PROCESS value VALUES
        $gallerytable = $this->resourceConnection->getTableName('catalog_product_entity_media_gallery_value');
        
        $write->query("DELETE FROM $gallerytable
        WHERE store_id = $to_store");


        $write->query("INSERT INTO $gallerytable (
    	`value_id`, 
        `store_id`, 
        `entity_id`, 
        `label`, 
        `position`, 
        `disabled`
    	) SELECT
    	value_id,
        $to_store,
    	entity_id,
    	label,
    	position,
    	disabled
    	FROM $gallerytable
    	WHERE store_id = $from_store");


        /** catalog_product_entity_media_gallery_value_video */
        # PROCESS value VALUES
        $videotable = $this->resourceConnection->getTableName('catalog_product_entity_media_gallery_value_video');
        $write->query("DELETE FROM $videotable
        WHERE store_id = $to_store");


        $write->query("INSERT INTO $videotable (
    	store_id,
    	value_id,
    	provider,
    	url,
    	title,
        description,
        metadata
    	) SELECT
    	$to_store,
    	value_id,
    	provider,
    	url,
    	title,
        description,
        metadata
    	FROM $videotable
    	WHERE store_id = $from_store");



        /** catalog_product_option */
        # PROCESS price VALUES
        $pricetable = $this->resourceConnection->getTableName('catalog_product_option_price');
        
        $write->query("DELETE FROM $pricetable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $pricetable (
    	store_id,
    	option_id,
    	price,
    	price_type
    	) SELECT
    	$to_store,
    	option_id,
    	price,
    	price_type
    	FROM $pricetable
    	WHERE store_id = $from_store");

        # PROCESS title VALUES
        $optitletable = $this->resourceConnection->getTableName('catalog_product_option_title');
        $write->query("DELETE FROM $optitletable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $optitletable (
    	store_id,
    	option_id,
    	title
    	) SELECT
    	$to_store,
    	option_id,
    	title
    	FROM $optitletable
    	WHERE store_id = $from_store");

        # PROCESS type_price VALUES
        $oppricetable = $this->resourceConnection->getTableName('catalog_product_option_type_price');
        $write->query("DELETE FROM $oppricetable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $oppricetable (
    	store_id,
    	option_type_id,
    	price,
    	price_type
    	) SELECT
    	$to_store,
    	option_type_id,
    	price,
    	price_type
    	FROM $oppricetable
    	WHERE store_id = $from_store");

        # PROCESS type_title VALUES
        $optypetitletable = $this->resourceConnection->getTableName('catalog_product_option_type_title');
        $write->query("DELETE FROM $optypetitletable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $optypetitletable (
    	store_id,
    	option_type_id,
    	title
    	) SELECT
    	$to_store,
    	option_type_id,
    	title
    	FROM $optypetitletable
    	WHERE store_id = $from_store");

        /** catalog_product_super_attribute */
        # PROCESS label VALUES
        $seavlbltable = $this->resourceConnection->getTableName('catalog_product_super_attribute_label');
        $write->query("DELETE FROM $seavlbltable
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $seavlbltable (
    	store_id,
    	product_super_attribute_id,
    	use_default,
    	value
    	) SELECT
    	$to_store,
    	product_super_attribute_id,
    	use_default,
    	value
    	FROM $seavlbltable
    	WHERE store_id = $from_store");

        /** catalog_product_bundle_option * */
        # PROCESS value VALUES
        $bndltable = $this->resourceConnection->getTableName('catalog_product_bundle_option_value');
        $write->query("DELETE FROM $bndltable 
    	WHERE store_id = $to_store");

        $write->query("INSERT INTO $bndltable  (
    	store_id,
    	option_id,
    	title
    	) SELECT
    	$to_store,
    	option_id,
    	title
    	FROM $bndltable 
    	WHERE store_id = $from_store");

        $this->messageManager->addSuccess(__('Products successfully copied.'));
    }

}
