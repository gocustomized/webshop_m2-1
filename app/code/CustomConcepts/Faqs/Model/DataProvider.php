<?php
namespace CustomConcepts\Faqs\Model;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $dataPersistor;
    protected $loadedData;
    protected $request;
    protected $resourceHelper;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \CustomConcepts\Faqs\Model\ResourceModel\ProdfaqsTopics\Collection $topicsCollection,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\RequestInterface $request,
        \CustomConcepts\Faqs\Helper\Resource $resourceHelper,
        array $meta = [],
        array $data = []
    ){
        $this->collection = $topicsCollection;
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        $this->resourceHelper = $resourceHelper;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $topicId = $this->request->getParam('topic_id');
        if($topicId){ //form data
            $prodfaqsTopicCollection = $this->collection->addFieldToFilter('topic_id', $topicId);
            foreach ($prodfaqsTopicCollection as $prodfaqsTopic){
                $this->loadedData[$prodfaqsTopic->getId()] = $prodfaqsTopic->getData();
                $this->loadedData[$prodfaqsTopic->getId()]['store_id'] = $this->resourceHelper->getTopicStores($prodfaqsTopic->getId());

                // set image
                if ($prodfaqsTopic->getImage() != '') {
                    $this->loadedData[$prodfaqsTopic->getId()]['image_upload'][0] = [
                        'name' => $prodfaqsTopic->getTitle(),
                        'url' => $prodfaqsTopic->getImage()
                    ];
                }
            }
        } else { //grid data
            $prodfaqsTopicsCollection = $this->collection->toArray();
            $this->loadedData['totalRecords'] = $prodfaqsTopicsCollection['totalRecords'];
            foreach ($prodfaqsTopicsCollection['items'] as $prodfaqsTopics){
                $prodfaqsTopics['store_id'] = $this->resourceHelper->getTopicStores($prodfaqsTopics['topic_id']);
                $this->loadedData['items'][] = $prodfaqsTopics;
            }
        }
        if($this->loadedData['totalRecords'] <= 0){
            $this->loadedData['items']=[];
        }
        return $this->loadedData;
    }
}
