<?php
namespace CustomConcepts\HelpCenter\Controller\ContactForm;

use Magento\Framework\App\Action\Context;
use Xtento\OrderExport\Invition\Exception;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;

class Login extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;
    /**
     * @var CustomerUrl
     */
    protected $customerUrl;
    protected $cookieMetadataManager;
    protected $cookieMetadataFactory;

    /**
     * Login constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerHelperData
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData
    )
    {
        parent::__construct($context);

        $this->jsonResultFactory = $jsonResultFactory;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerUrl = $customerHelperData;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $json = $this->jsonResultFactory->create();

        $result = ['success' => false];

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->customerSession->setCustomerDataAsLoggedIn($customer);
                    if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                        $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                        $metadata->setPath('/');
                        $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                    }

                    $result['redirect'] = $this->_url->getUrl('*/*', ['_secure' => true]);
                    $result['success'] = true;
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (AuthenticationException $e) {
                    $message = __(
                        'The account sign-in was incorrect or your account is disabled temporarily. '
                        . 'Please wait and try again later.'
                    );
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $message = __('An unspecified error occurred. Please contact us for assistance.');
                } finally {
                    if (isset($message)) {
                        $result['error'] = $message;
                        $this->customerSession->setUsername($login['username']);
                    }
                }
            } else {
                $result['error'] = __('Login and password are required.');
            }
        }

        return $json->setData($result);
    }

    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }
}
