<?php

namespace Oscprofessionals\OscShop\Model\Config\Source;

class Token implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Oscprofessionals\OscShop\Helper\Data
     */
    private $dataHelper;

    /**
     * Token constructor.
     * @param \Oscprofessionals\OscShop\Helper\Data $helperData
     */
    public function __construct(
        \Oscprofessionals\OscShop\Helper\Data $helperData
    ) {
        $this->dataHelper = $helperData;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $data = $this->dataHelper->getTokenDetails();
        if (!empty($data)) {
            $data;
        } else {
            $data = array('Sorry, there is no any token generated.');
        }
        return $data;
    }
}
