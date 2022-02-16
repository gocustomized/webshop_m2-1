<?php

/**
 * CustomConcepts_Oathlogin extension
 * @category  CustomConcepts_Extensions
 * @package   CustomConcepts_Oathlogin
 * @copyright Copyright (c) 2017
 * @author GoCustomized <info@gocustomized.com>
 */

namespace CustomConcepts\Oathlogin\Model\System\Config\Source;

class Role {
    
    /**
     *
     * @var type \Magento\Authorization\Model\Role
     */
    protected $role;
    /**
     * 
     * @param \Magento\Authorization\Model\Role $role
     */        
    public function __construct(
        \Magento\Authorization\Model\Role $role
    ) {
        $this->role = $role;
    }
    
    /**
     * 
     * @return array
     */
    public function toOptionArray() {
        $collection = $this->role->getCollection()
                    ->addFieldToFilter('role_type','G');
        $option = [];
        foreach($collection as $role){
            $role_id = $role->getRoleId();
            $role_name = $role->getRoleName();
            $push_array = ['value'=> $role_id ,'label'=> $role_name ];
            array_push($option, $push_array);
        }
        
        return $option;
    }
}