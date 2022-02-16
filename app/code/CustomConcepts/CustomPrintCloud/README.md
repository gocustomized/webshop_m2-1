# Mage2 Module CustomConcepts CustomPrintCloud

    ``customconcept/module-customprintcloud``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Module for cpc widgets

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/CustomConcepts`
 - Enable the module by running `php bin/magento module:enable CustomConcepts_CustomPrintCloud`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require customconcept/module-customprintcloud`
 - enable the module by running `php bin/magento module:enable CustomConcepts_CustomPrintCloud`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - api_key (products/general/api_key)

 - api_secret (products/general/api_secret)


## Specifications

 - Helper
	- CustomConcepts\CustomPrintCloud\Helper\CPC

 - Observer
	- cpc_catalog_category_view > CustomConcepts\CustomPrintCloud\Observer\Frontend\Cpc\CatalogCategoryView

 - Block
	- CategoryPage > categorypage.phtml

 - Block
	- ProductPage > productpage.phtml


## Attributes

 - Category - CPC_widget (cpc_widget)

