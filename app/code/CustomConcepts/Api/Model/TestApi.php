<?php
namespace CustomConcepts\Api\Model;


class TestApi
{
    protected $restRequest;

    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $restRequest
    ){
        $this->restRequest = $restRequest;
    }

    /**
     * @return string[]
     */
    public function execute(){
        $request = $this->restRequest->getBodyParams();

        $this->log($request);

//        $response = [
//            'haha' => 'hehe'
//        ];
        return $request;
    }

    /**
     * @param $message
     * @return $this
     */
    public function log($message){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mendzAPI.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);

        return $this;
    }
}
