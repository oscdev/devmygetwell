<?php
namespace Oscprofessionals\PaymentPaypal\Model\Source;

class Mode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {

        return [
            ['value' => 0, 'label' => __('Test')],
            ['value' => 1, 'label' => __('Live')],
        ];
    }
}