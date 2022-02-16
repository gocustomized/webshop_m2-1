<?php
/**
 * CustomConcepts_Theme extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Theme
 * @copyright Copyright (c) 2020
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\Theme\Block\Html\Header;

/**
 * Mobile Logo page header block
 */
class MobileLogo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageHelper,
        array $data = []
    ) {
        $this->_fileStorageHelper = $fileStorageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get logo image URL
     *
     * @return string
     */
    public function getMobileLogoSrc()
    {
        if (empty($this->_data['mobile_logo_src'])) {
            $this->_data['mobile_logo_src'] = $this->_getMobileLogoUrl();
        }
        return $this->_data['mobile_logo_src'];
    }

    /**
     * Retrieve logo text
     *
     * @return string
     */
    public function getLogoAlt()
    {
        if (empty($this->_data['logo_alt'])) {
            $this->_data['logo_alt'] = $this->_scopeConfig->getValue(
                'design/header/logo_alt',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->_data['logo_alt'];
    }

    /**
     * Retrieve logo image URL
     *
     * @return string
     */
    protected function _getMobileLogoUrl()
    {
        $folderName = \Magento\Theme\Model\Design\Backend\Logo::UPLOAD_DIR;
        $folderNameMobile = \CustomConcepts\Theme\Model\Design\Backend\MobileLogo::UPLOAD_DIR;

        $storeLogoPath = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $storeMobileLogoPath = $this->_scopeConfig->getValue(
            'design/header/mobile_logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $path = $folderNameMobile . '/' . $storeMobileLogoPath;
        $url = null;

        if (!is_null($storeMobileLogoPath) && $this->_isFile($path)) { // Use mobile logo if available
            $url = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        } else { // Use default logo if mobile logo is not available
            $path = $folderName . '/' . $storeLogoPath;
            $url = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        }
        return $url;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename relative path
     * @return bool
     */
    protected function _isFile($filename)
    {
        if ($this->_fileStorageHelper->checkDbUsage() && !$this->getMediaDirectory()->isFile($filename)) {
            $this->_fileStorageHelper->saveFileToFilesystem($filename);
        }

        return $this->getMediaDirectory()->isFile($filename);
    }
}
