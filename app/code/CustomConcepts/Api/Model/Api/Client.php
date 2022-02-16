<?php
namespace CustomConcepts\Api\Model\Api;


use Magento\Framework\Exception\LocalizedException;

class Client
{
    const ACCESS_TOKEN_URL = 'rest/V1/integration/customer/token';
    const CREATE_CART_URL = 'rest/V1/carts/mine';
    const ADD_TO_CART_URL = 'rest/V1/carts/mine/items';
    const ADD_SHIPPING_INFO_URL = 'rest/V1/carts/mine/shipping-information';
    const CREATE_ORDER_URL = 'rest/V1/carts/mine/order';

    protected $client;
    protected $url;
    protected $token;

    public function __construct(
        \Magento\Framework\UrlInterface $url
    ){
        $this->url = $url;
    }

    public function curlExec($method, $requestBody = null, $requestType = 'GET'){
        $curlOptions = [
            CURLOPT_URL            => $this->url->getUrl() . $method,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->getToken()
            ],
            // connection settings
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
        ];
        if($requestType == 'POST'){
            $curlOptions[CURLOPT_POST] = 1;
            $curlOptions[CURLOPT_POSTFIELDS] = $requestBody;
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        } elseif($requestType == 'PUT'){
            $curlOptions[CURLOPT_CUSTOMREQUEST] = "PUT";
            $curlOptions[CURLOPT_POSTFIELDS] = $requestBody;
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        } else { //GET
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Accept: application/json';
        }


        // initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $responseBody = $this->executeRequest($ch, $requestBody);

        // collect details and close cURL resource
        $curlError = curl_error($ch);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false) {
            throw new LocalizedException(__('GoCase API connection failure: %1', $curlError));
        }

        $responseBody = json_decode($responseBody, true);

        if ($httpResponseCode < 200 || $httpResponseCode >= 300) {
            if(isset($responseBody['message'])){
                throw new LocalizedException(__($responseBody['message']));
            } else {
                throw new LocalizedException(__('an error occurs'));
            }
        }

        return $responseBody;
    }


    public function executeRequest($ch, $requestBody = null)
    {
        $time = microtime(true);
        $responseBody = curl_exec($ch);
        $time = microtime(true) - $time;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cc_api.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(sprintf(
            "REQUEST: %s\nREQUEST BODY: %s\nDURATION: %0.4f sec\nRESPONSE CODE: %s\nRESPONSE BODY: %s",
            rtrim(curl_getinfo($ch, CURLINFO_HEADER_OUT)),
            $requestBody,
            $time,
            curl_getinfo($ch, CURLINFO_HTTP_CODE),
            $responseBody
        ));

        return $responseBody;
    }

    public function generateAccessToken($username, $password){
        $requestBody = ['username' => $username, 'password' => $password];

        $curlOptions = [
            // HTTP request
            CURLOPT_URL            => $this->url->getUrl() . self::ACCESS_TOKEN_URL,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json',
            ],
            // connection settings
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $requestBody,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
        ];

        // initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        $responseBody = $this->executeRequest($ch);

        // collect details and close cURL resource
        $curlError = curl_error($ch);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return str_replace('"', '', $responseBody);
    }

    public function setToken($token){
        $this->token = $token;
    }

    public function getToken(){
        return $this->token;
    }

    public function createQuote($quoteRequest){
        return $this->curlExec(self::CREATE_CART_URL, $quoteRequest, 'POST');
    }

    public function addItemToCart($item){
        return $this->curlExec(self::ADD_TO_CART_URL, $item, 'POST');
    }

    public function addShippingInfo($shippingInfo){
        return $this->curlExec(self::ADD_SHIPPING_INFO_URL, $shippingInfo, 'POST');
    }

    public function createOrder($orderRequest){
        return $this->curlExec(self::CREATE_ORDER_URL, $orderRequest, 'PUT');
    }
}
