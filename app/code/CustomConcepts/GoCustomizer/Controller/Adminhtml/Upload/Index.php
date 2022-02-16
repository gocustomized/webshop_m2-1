<?php
namespace CustomConcepts\GoCustomizer\Controller\Adminhtml\Upload;

use Magento\Framework\App\Filesystem\DirectoryList;

class Index extends \Magento\Backend\App\Action {
    /*
     * \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */

    protected $_fileUploaderFactory;

    /*
     * \Magento\Framework\Filesystem $_filesystem
     */
    protected $_filesystem;

    /**
     *  \Magento\Framework\Message\ManagerInterface  $messageManager
     */
    protected $messageManager;

    /**
     *  \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     *  \Magento\Backend\Model\Session\Quote $session
     */
    protected $session;

    /**
     * \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $orderItemRepository;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Session\Quote $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $_filesystem,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    ) {

        $this->session = $session;
        $this->_filesystem = $_filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->messageManager = $context->getMessageManager();
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderItemRepository = $orderItemRepository;

        parent::__construct($context);
    }

//    public function _isAllowed() { //need to create an ACL for this
//        return $this->_authorization->isAllowed('CustomConcept_ExtendedQuote::upload');
//    }

    /**
     *
     * Upload file with processing quote item
     */
    public function execute() {
        //upload a file
        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'finalimage']);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $uploader->setAllowRenameFiles(false);
        //set false as we want to save in custom folder
        $uploader->setFilesDispersion(false);
        $fileExt = $uploader->getFileExtension();

        //path to store a uploaded file
        $folderpath = 'customer_data/orders/' . date("Ymd") . '/';
        $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath($folderpath);

        $newfilename = time() . '.' . $fileExt;
        $result = $uploader->save($path, $newfilename);

        if (!empty($result) && $result['file'] != '') {
            //upload a file for processing the quote item
            try {
                $itemId = $this->getQuoteItemId();
                $actionType = $this->getRequest()->getParam('action_type');

                if($actionType == 'quote'){
                    //get current quote from session
                    $quote = $this->session->getQuote();
                    $quoteItem = $quote->getItemById($itemId);
                } elseif($actionType == 'order') {
                    $quoteItem = $this->orderItemRepository->get($itemId);
                }

                $file = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $folderpath . $newfilename;
                //update customizerdata
                $customizerData = serialize([
                        'design' => $file,
                        'thumb' => $file,
                        'session' => $this->session->getSessionId(),
                        'design_engine_url' => $file
                    ]
                );
                //save
                $quoteItem->setGocustomizerData($customizerData)->save();
                return $this->resultJsonFactory->create()->setData(['success' => 1, 'image' => $file, 'message' => null]);
            } catch (\Exception $e) {
                return $this->resultJsonFactory->create()->setData(['success' => 0, 'image' => null, 'message' => __($e->getMessage())]);
            }
        }
    }

    /**
     * Get the quote item id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getQuoteItemId() {
        $quoteItemId = (int) $this->getRequest()->getParam('item_id');
        if (!$quoteItemId) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Quote item id is not received.'));
        }

        return $quoteItemId;
    }

}
