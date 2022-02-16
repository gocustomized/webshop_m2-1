<?php

namespace CustomConcepts\Wics\Model;

use CustomConcepts\Wics\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\ScopeInterface;

class SlackAdapter
{
    const XML_PATH_TO_API_CONFIG = 'cc_wics/slack';
    const SUCCESS_RESPONSE_STATUS_CODE = 200;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $webHook = '';

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Curl $curl
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     */
    public function __construct(Curl $curl, ScopeConfigInterface $scopeConfig, Logger $logger)
    {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @param $path
     * @param bool $required
     * @return mixed
     */
    private function getConfigValue($path, $required = true)
    {
        if ($required && !$this->scopeConfig->isSetFlag($path)) {
            throw new \RuntimeException("Config path '$path' not found!");
        }

        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Read configuration and init curl
     */
    private function initConfig()
    {
        if ($this->webHook) {
            //we imply config is already initialized
            return;
        }
        $this->webHook = $this->getConfigValue(self::XML_PATH_TO_API_CONFIG . "/web_hook");

        $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
    }

    public function send(string $message)
    {
        if (!$this->getConfigValue(self::XML_PATH_TO_API_CONFIG . "/enabled", false)) {
            // slack notification disabled
            return;
        }
        $this->initConfig();
        $this->curl->post($this->webHook, ['payload' => json_encode(['text' => $message])]);

        if ($this->curl->getStatus() != self::SUCCESS_RESPONSE_STATUS_CODE) {
            $this->logger->notice("Slack connection failed - {$this->curl->getBody()}");
        }
    }
}
