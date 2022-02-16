<?php
namespace CustomConcepts\Faqs\Controller\adminhtml;

use Magento\Backend\App\Action;

abstract class Manage extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'CustomConcepts_Faqs::faq';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;
    /**
     * @var \CustomConcepts\Faqs\Model\ProdfaqsTopicsFactory
     */
    protected $prodfaqsTopicModelFactory;
    /**
     * @var \CustomConcepts\Faqs\Model\ProdfaqsFactory
     */
    protected $prodfaqsModelFactory;
    /**
     * @var \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics
     */
    protected $prodfaqsTopicResource;
    /**
     * @var \CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs
     */
    protected $prodfaqsResource;
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    /**
     * @var \CustomConcepts\Faqs\Helper\Uploader
     */
    protected $uploaderHelper;

    /**
     * Manage constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \CustomConcepts\Faqs\Model\ProdfaqsTopicsFactory $prodfaqsTopicModelFactory
     * @param \CustomConcepts\Faqs\Model\ProdfaqsFactory $prodfaqsModelFactory
     * @param \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics $prodfaqsTopicResource
     * @param \CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs $prodfaqsResource
     * @param \CustomConcepts\Faqs\Helper\Uploader $uploaderHelper
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \CustomConcepts\Faqs\Model\ProdfaqsTopicsFactory $prodfaqsTopicModelFactory,
        \CustomConcepts\Faqs\Model\ProdfaqsFactory $prodfaqsModelFactory,
        \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics $prodfaqsTopicResource,
        \CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs $prodfaqsResource,
        \CustomConcepts\Faqs\Helper\Uploader $uploaderHelper
    ){
        $this->pageFactory = $pageFactory;
        $this->prodfaqsTopicModelFactory = $prodfaqsTopicModelFactory;
        $this->prodfaqsModelFactory = $prodfaqsModelFactory;
        $this->prodfaqsTopicResource = $prodfaqsTopicResource;
        $this->prodfaqsResource = $prodfaqsResource;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->uploaderHelper = $uploaderHelper;
        parent::__construct($context);
    }
}
