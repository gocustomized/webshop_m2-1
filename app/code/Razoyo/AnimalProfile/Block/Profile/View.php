<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Razoyo\AnimalProfile\Block\Profile;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ScustomerSession
     */
    private $customerSession;

    protected $animals;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Razoyo\AnimalProfile\Model\Animals\Source\Animals $animals,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->animals = $animals;
    }

    public function getGreeting()
    {
        return 'Hello ' . $this->customerSession->getCustomer()->getFirstname() . '!';
    }

    public function getPhotoUrl()
    {
        return $this->getUrl('animalid/profile/photo');
    }

    public function getAnimalOptions()
    {
        return $this->animals->toOptionArray();
    }
}

