<?php
namespace CustomConcepts\Checkout\Model;
use Bss\MultiStoreViewPricing\Model\Checkout\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class CCSession extends Session
{
    /**
     * @var LoggerInterface
     */
    private $ccLogger;

    /**
     * CCSession constructor.
     * @param Http $request
     * @param SidResolverInterface $sidResolver
     * @param ConfigInterface $sessionConfig
     * @param SaveHandlerInterface $saveHandler
     * @param ValidatorInterface $validator
     * @param StorageInterface $storage
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param State $appState
     * @param OrderFactory $orderFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CartRepositoryInterface $quoteRepository
     * @param RemoteAddress $remoteAddress
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteFactory $quoteFactory
     * @param LoggerInterface $logger
     * @throws \Magento\Framework\Exception\SessionException
     */
    public function __construct(
        Http $request,
        SidResolverInterface $sidResolver,
        ConfigInterface $sessionConfig,
        SaveHandlerInterface $saveHandler,
        ValidatorInterface $validator,
        StorageInterface $storage,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        State $appState,
        OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customerSession,
        CartRepositoryInterface $quoteRepository,
        RemoteAddress $remoteAddress,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteFactory $quoteFactory,
        LoggerInterface $logger = null
    )
    {
        if(!$logger){
            $logger = \Magento\Framework\App\ObjectManager::getInstance()->get('\Psr\Log\LoggerInterface');

        }
        $this->ccLogger = $logger;

        parent::__construct($request,
                            $sidResolver,
                            $sessionConfig,
                            $saveHandler,
                            $validator,
                            $storage,
                            $cookieManager,
                            $cookieMetadataFactory,
                            $appState,
                            $orderFactory,
                            $customerSession,
                            $quoteRepository,
                            $remoteAddress,
                            $eventManager,
                            $storeManager,
                            $customerRepository,
                            $quoteIdMaskFactory,
                            $quoteFactory,
                            $logger);

    }

    /**
     * description: Added logic for registered customer on checkout and making sure if payment gets cancelled that quote is still working and can be ordered with.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function restoreQuote(){
        /** @var \Magento\Sales\Model\Order $order */
        $this->ccLogger->debug('Called CC restore quote......');
        $order = $this->getLastRealOrder();
        if ($order->getId()) {
            try {
                $quote = $this->quoteRepository->get($order->getQuoteId());

                /** Start Custom logic to add customer id to quote and set quote to not guest. */
                if($id = $this->_customerSession->getCustomerId()){
                    $quote->setCustomerId($id)->setCustomerIsGuest(0)->setCheckoutMethod('customer');
                }
                /** End Custom logic to add customer id to quote and set quote to not guest. */

                $quote->setIsActive(1)->setReservedOrderId(null);
                $this->quoteRepository->save($quote);
                $this->replaceQuote($quote)->unsLastRealOrderId();
                $this->_eventManager->dispatch('restore_quote', ['order' => $order, 'quote' => $quote]);
                return true;
                } catch (NoSuchEntityException $e) {
                    $this->ccLogger->critical($e);
                }
        }

        return false;
    }
}
