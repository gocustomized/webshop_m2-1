<?php
namespace CustomConcepts\Faqs\Model\Prodfaqs;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $loadedData;
    protected $request;
    protected $resourceHelper;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \CustomConcepts\Faqs\Model\ResourceModel\Prodfaqs\Collection $faqsCollection,
        \Magento\Framework\App\RequestInterface $request,
        \CustomConcepts\Faqs\Helper\Resource $resourceHelper,
        array $meta = [],
        array $data = []
    ){
        $this->collection = $faqsCollection;
        $this->request = $request;
        $this->resourceHelper = $resourceHelper;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $faqsId = $this->request->getParam('faqs_id');
        if($faqsId){ //form data
            $prodfaqsCollection = $this->collection->addFieldToFilter('faqs_id', $faqsId);
            foreach ($prodfaqsCollection as $prodfaqs){
                $this->loadedData[$prodfaqs->getId()] = $prodfaqs->getData();
            }
        } else { //grid data
            $prodfaqsCollection = $this->collection->toArray();
            $this->loadedData['totalRecords'] = $prodfaqsCollection['totalRecords'];
            foreach ($prodfaqsCollection['items'] as $prodfaqs){
                $prodfaqs['store_id'] = $this->resourceHelper->getTopicStores($prodfaqs['topic_id']);
                $this->loadedData['items'][] = $prodfaqs;
            }
        }
        if(empty($this->loadedData) || !array_key_exists('totalRecords', $this->loadedData) || $this->loadedData['totalRecords'] <= 0){
            $this->loadedData['items']=[];
        }

        return $this->loadedData;
    }
}
