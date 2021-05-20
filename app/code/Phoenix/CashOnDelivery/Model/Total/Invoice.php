<?php
/**
 * Phoenix Cash on Delivery module for Magento 2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Phoenix_CashOnDelivery
 * @copyright  Copyright (c) 2017 Phoenix Media GmbH (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Phoenix\CashOnDelivery\Model\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Phoenix\CashOnDelivery\Helper\Data;

class Invoice extends AbstractTotal
{
    /**
     * @var Data $helper
     */
    protected $helper;

    public function __construct(
        Data $helper,
        array $data = []
    ) {
        parent::__construct($data);
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return self
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        if (!$this->helper->isActiveMethod($invoice->getOrder())) {
            return $this;
        }

        $baseCodFee = $invoice->getBaseCodFee();
        $codFee = $invoice->getCodFee();

        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseCodFee);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $codFee);

        return $this;
    }
}
