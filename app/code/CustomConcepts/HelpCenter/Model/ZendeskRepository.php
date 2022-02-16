<?php
namespace CustomConcepts\HelpCenter\Model;

class ZendeskRepository
{
    /**
     * @var \Zendesk\Zendesk\Helper\Api
     */
    protected $zendeskApiHelper;
    protected $zendeskApiClient;


    /**
     * ZendeskRepository constructor.
     * @param \Zendesk\Zendesk\Helper\Api $zendeskApiHelper
     */
    public function __construct(
        \Zendesk\Zendesk\Helper\Api $zendeskApiHelper
    ){
        $this->zendeskApiHelper = $zendeskApiHelper;

        $this->getClient();
    }

    private function getClient(){
        try {
            $this->zendeskApiClient = $this->zendeskApiHelper->getZendeskApiInstance();
        } catch (\Exception $e){
            // log error
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createTicket($data){
        return $this->zendeskApiClient->post('api/v2/tickets.json', $data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function createOrUpdateUser($data){
        return $this->zendeskApiClient->post('api/v2/users/create_or_update.json', $data);
    }
}
