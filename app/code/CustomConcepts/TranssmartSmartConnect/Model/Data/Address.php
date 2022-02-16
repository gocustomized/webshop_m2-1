<?php
namespace CustomConcepts\TranssmartSmartConnect\Model\Data;

/**
 * Overriden data container or address to add eoriNumber to sender address field.
 */

class Address extends \Bluebirdday\TranssmartSmartConnect\Model\Data\Address
{
    /**
     * @var array
     */
    protected $requiredFields = [
        'type',
        'name',
        'addressLine1',
        'houseNo',
        'city',
        'zipCode',
        'country',
    ];

    /**
     * @var array
     */
    protected $optionalFields = [
        'addressLine2',
        'addressLine3',
        'state',
        'contact',
        'telNo',
        'faxNo',
        'email',
        'accountNumber',
        'customerNumber',
        'vatNumber',
        'residential',
        'eoriNumber'
    ];

    public function getEoriNumber()
    {
        return $this->getData('eoriNumber');
    }
}
