<?php
namespace CustomConcepts\GoCustomizer\Helper;

use Magento\Framework\App\Helper\Context;
use Xtento\OrderExport\Invition\Exception;

class Client extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var mixed|string
     */
    protected $url = '';
    /**
     * @var mixed|string
     */
    protected $apiKey = '';

    /**
     * Client constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
        $this->url = $this->scopeConfig->getValue('gocustomizer/general/customizer_url');
        $this->apiKey = $this->scopeConfig->getValue('gocustomizer/general/reseller_api_key');
    }

    /**
     * @param $method
     * @param null $requestBody
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Json_Exception
     */
    protected function _curlExec($method, $requestBody = null)
    {
        $curlOptions = array(
            // HTTP request
            CURLOPT_URL            => $this->url . $method,
            CURLOPT_HTTPHEADER     => array(
                'Accept: application/json',
            ),
            // connection settings
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 20,
        );
        if (!is_null($requestBody)) {
            $curlOptions[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
            $curlOptions[CURLOPT_POST]         = true;
            $curlOptions[CURLOPT_POSTFIELDS]   = $requestBody;
        }

        // initialize cURL
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        // execute the request
        $time = microtime(true);
        $responseBody = curl_exec($ch);
        $time = microtime(true) - $time;

        // write request details and response to log
        $this->_logger->debug(sprintf("REQUEST: %s\nREQUEST BODY: %s\nDURATION: %0.4f sec\nRESPONSE CODE: %s\nRESPONSE BODY: %s",rtrim(curl_getinfo($ch,CURLINFO_HEADER_OUT)),$requestBody,$time,curl_getinfo($ch,CURLINFO_HTTP_CODE),$responseBody));

        // collect details and close cURL resource
        $curlError = curl_error($ch);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // check for cURL failure
        if ($responseBody === false) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Gocustomizer API connection failure: ' . $curlError));
        }

        // check for unexpected HTTP response code
        if ($httpResponseCode < 200 || $httpResponseCode >= 300) {
            // try to extract JSON encoded message from response body
            $message = false;
            if ($responseBody !== false) {
                $message = str_replace(array("\r", "\n"), ' ', strip_tags($responseBody));
                try {
                    $response = \Zend_Json_Decoder::decode($responseBody);
                    if (is_string($response)) {
                        $message = $response;
                    }
                    elseif (is_array($response) && isset($response['Message'])) {
                        $message = $response['Message'];
                    }
                }
                catch (Zend_Json_Exception $exception) {
                    $this->_logger->critical($exception);
                }
            }

            if ($message) {

                $this->_logger->debug(__('Gocustomizer API error: ' . $message));
                throw new \Magento\Framework\Exception\LocalizedException(__('Transsmart API error: ' . $message));
            }
            else {
                $this->_logger->debug(__('Gocustomizer API returned unexpected HTTP response code: ' . $httpResponseCode));
                throw new \Magento\Framework\Exception\LocalizedException(__('Gocustomizer API returned unexpected HTTP response code: ' . $httpResponseCode));
            }
        }

        return $responseBody;
    }

    /**
     * @param $auth
     * @param $session
     * @return array|mixed|\StdClass|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Json_Exception
     */
    public function saveFinalDesign($auth, $session){
        $res_body = $this->_curlExec('api/v1/saveFinalImage/'.$auth.'/'.$session.'?api_key='.$this->apiKey);

        return \Zend_Json_Decoder::decode($res_body);
    }

}
