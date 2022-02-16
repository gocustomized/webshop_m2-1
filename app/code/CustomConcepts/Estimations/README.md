# Mage2 Module CustomConcepts Estimations

    ``customconcepts/module-estimations``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Module for Estimations of Shipping and Delivery

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/CustomConcepts`
 - Enable the module by running `php bin/magento module:enable CustomConcepts_Estimations`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require customconcepts/module-estimations`
 - enable the module by running `php bin/magento module:enable CustomConcepts_Estimations`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Helper
	- CustomConcepts\Estimations\Helper\Estimation

 - Helper
	- CustomConcepts\Estimations\Helper\ShippingDate

 - Helper
	- CustomConcepts\Estimations\Helper\DeliveryDate

 - Observer
	- sales_quote_add_item > CustomConcepts\Estimations\Observer\Sales\QuoteAddItem

 - Plugin
	- afterSaveAddressInformation - Magento\Checkout\Model\ShippingInformationManagement > CustomConcepts\Estimations\Plugin\Magento\Checkout\Model\ShippingInformationManagement

 - Block
	- EstimationDatesGrid > estimationdatesgrid.phtml


## Attributes



