<?php
namespace CustomConcepts\Faqs\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Uploader extends \Magento\Framework\App\Helper\AbstractHelper {
    const BASE_TOPIC_MEDIA_PATH_TMP                       = 'faq_topics/tmp/';
    const BASE_TOPIC_MEDIA_PATH                           = 'faq_topics/';

    /**
     * @var \Magento\Catalog\Model\ImageUploader
     */
    protected $imageUploader;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Uploader constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ImageUploader $imageUploader
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Magento\Framework\Filesystem $filesystem
    ){
        $this->imageUploader = $imageUploader;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getMediaPath(){
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }

    /**
     * @param string $basePath
     * @param string $tempPath
     * @return \Magento\Catalog\Model\ImageUploader
     */
    public function getImageUploader($basePath = '', $tempPath = ''){
        $this->imageUploader->setBaseTmpPath(self::BASE_TOPIC_MEDIA_PATH_TMP);
        $this->imageUploader->setBasePath(self::BASE_TOPIC_MEDIA_PATH);
        return $this->imageUploader;
    }

    /**
     * @return string
     */
    public function getTopicMediaPathTmp(){
        return self::BASE_TOPIC_MEDIA_PATH_TMP;
    }

    public function clearTmpDir(){
        $mediapath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $files = glob($mediapath . self::BASE_TOPIC_MEDIA_PATH_TMP . '*'); // get all file names
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }
        }
    }

    public function deleteImage($image){ //image value is media URL
        $pos = strpos($image, 'faq_topics/');
        $path = substr($image, $pos);

        $fullPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path);
        if(is_file($fullPath)){
            unlink($fullPath);
        }
    }

}
