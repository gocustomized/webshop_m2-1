<?php
/**
 * CustomConcepts_Theme extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Theme
 * @copyright Copyright (c) 2020
 * @author GoCustomized <info@gocustomized.com>
 */
namespace CustomConcepts\Theme\Model\Design\Backend;

use Magento\Theme\Model\Design\Backend\Image;

class MobileLogo extends Image
{
    /**
     * The tail part of directory path for uploading
     *
     */
    const UPLOAD_DIR = 'mobile_logo';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getRelativePath($this->_appendScopeInfo(static::UPLOAD_DIR));
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }
}