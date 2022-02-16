<?php
namespace CustomConcepts\Klaviyo\Helper;

use Klaviyo\Reclaim\Helper\Logger;
use Klaviyo\Reclaim\Helper\ScopeSetting;
use Magento\Framework\App\Helper\Context;

class Data extends \Klaviyo\Reclaim\Helper\Data
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface  */
    protected $customerRepository;

    /** @var \Magento\Customer\Api\GroupRepositoryInterface  */
    protected $groupRepository;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface  */
    protected $date;

    /**
     * Data constructor.
     * @param Context $context
     * @param Logger $klaviyoLogger
     * @param ScopeSetting $klaviyoScopeSetting
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     */
    public function __construct(
        Context $context,
        \Klaviyo\Reclaim\Helper\Logger $klaviyoLogger,
        \Klaviyo\Reclaim\Helper\ScopeSetting $klaviyoScopeSetting,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ){
        $this->customerRepository = $customerRepository;
        $this->groupRepository = $groupRepository;
        $this->date = $date;
        parent::__construct($context, $klaviyoLogger, $klaviyoScopeSetting);
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $source
     * @return bool|string
     */
    public function subscribeEmailToKlaviyoList($email, $firstName = null, $lastName = null, $source = null)
    {
        $listId = $this->_klaviyoScopeSetting->getNewsletter();
        $optInSetting = $this->_klaviyoScopeSetting->getOptInSetting();

        $properties = [];
        $properties['email'] = $email;
        if ($firstName) $properties['$first_name'] = $firstName;
        if ($lastName) $properties['$last_name'] = $lastName;
        if ($source) $properties['$source'] = $source;

        /** Set custom properties */
        $customer = $this->customerRepository->get($email);
        if($customer){
            $date = $this->date->date($customer->getCreatedAt());
            $properties['Magento Account Created'] = $date->format('F j, Y') . ' at ' . $date->format('h:i A');
            $properties['Magento Customer Group'] = $this->groupRepository->getById($customer->getGroupId())->getCode();
            $properties['Magento Store'] = $customer->getCreatedIn();
            $properties['Magento WebsiteID'] = $customer->getWebsiteId();
        }

        $propertiesVal = ['profiles' => $properties];

        $path = self::LIST_V2_API . $listId . $optInSetting;

        try {
            $response = $this->sendApiRequest($path, $propertiesVal, 'POST');
        } catch (\Exception $e) {
            $this->_klaviyoLogger->log(sprintf('Unable to subscribe %s to list %s: %s', $email, $listId, $e));
            $response = false;
        }

        return $response;
    }

    /**
     * @param string $path
     * @param array $params
     * @param string $method
     * @return bool|string
     * @throws \Exception
     */
    private function sendApiRequest(string $path, array $params, string $method = null)
    {
        $url = self::KLAVIYO_HOST . $path;

        //Add API Key to params
        $params['api_key'] = $this->_klaviyoScopeSetting->getPrivateApiKey();

        $curl = curl_init();
        $encodedParams = json_encode($params);

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => (!empty($method)) ? $method : 'POST',
            CURLOPT_POSTFIELDS => $encodedParams,
            CURLOPT_USERAGENT => self::USER_AGENT,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($encodedParams)
            ],
        ]);

        // Submit the request
        $response = curl_exec($curl);
        $err = curl_errno($curl);

        if ($err) {
            throw new \Exception(curl_error($curl));
        }

        // Close cURL session handle
        curl_close($curl);

        return $response;
    }
}
