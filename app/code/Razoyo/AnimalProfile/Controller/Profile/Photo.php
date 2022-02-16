<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Razoyo\AnimalProfile\Controller\Profile;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Razoyo\AnimalProfile\Animal;

class Photo implements HttpGetActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Http
     */
    protected $http;

    protected $request;

    protected $customerSession;

    protected $customerRepository;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param Json $json
     * @param LoggerInterface $logger
     * @param Http $http
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Json $json,
        LoggerInterface $logger,
        Http $http,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->serializer = $json;
        $this->logger = $logger;
        $this->http = $http;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
        if ($animal = $this->request->getParam('animal')){
            $customer->setCustomAttribute('animal_profile', $animal);
            $this->customerRepository->save($customer);
        } else {
            $animal = $customer->getCustomAttribute('animal_profile');
        }

        switch ($animal){
            case 'dog':
                $photo = new Animal\Dog;
                break;
            case 'llama':
                $photo = new Animal\Llama;
                break;
            case 'anteater':
                $photo = new Animal\Anteater;
                break;
            default: //cat
                $photo = new Animal\Cat();
        }

        try {
            return $this->jsonResponse(['photo' => $photo->getContent()]);
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return ResultInterface
     */
    public function jsonResponse($response = '')
    {
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Content-Type', 'application/json');
        return $this->http->setBody(
            $this->serializer->serialize($response)
        );
    }
}

