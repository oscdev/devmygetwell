<?php
namespace BoostMyShop\PointOfSales\Block\Customer\View;

class Information  extends AbstractBlock
{

    public function getInformation()
    {
        $information = [];
        foreach($this->getFields() as $field)
        {
            $information[$field['label']] = $this->getCustomer()->getData($field['name']);
        }
        return $information;
    }

    public function getFields()
    {
        $fields = [];

        $fields[] = ['label' => 'Created at', 'name' => 'created_at'];
        $fields[] = ['label' => 'Firstname', 'name' => 'firstname'];
        $fields[] = ['label' => 'Lastname', 'name' => 'lastname'];
        $fields[] = ['label' => 'Email', 'name' => 'email'];
        $fields[] = ['label' => 'Website', 'name' => 'website_id'];
        $fields[] = ['label' => 'Group', 'name' => 'group_id'];

        return $fields;
    }

}
