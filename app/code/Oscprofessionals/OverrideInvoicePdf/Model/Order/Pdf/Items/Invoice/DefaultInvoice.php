<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Oscprofessionals\OverrideInvoicePdf\Model\Order\Pdf\Items\Invoice;

/**
 * Sales Order Invoice Pdf default items renderer
 */
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice
{
    /**
     * Core string
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    protected $product;

    protected $taxclass;


    /**
     * DefaultInvoice constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\Tax\Model\TaxClass\Source\Product $taxclass
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Tax\Model\TaxClass\Source\Product $taxclass,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->product = $product;
        $this->taxclass = $taxclass;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $string,
            $resource,
            $resourceCollection,
            $data
        );
    }



    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];
        $product = $this->product->create()->load($item->getProductId());
        $productHsnCode = $product->getHsnCode();
        $specialPrice = $product->getSpecialPrice();
        $price = $product->getPrice();

        /* discount */
        if($specialPrice)
        {
            $discountAmount = $price - $specialPrice;
        }else{
            $discountAmount = '0';
        }
        /* discount */

        $taxClassId = $product['tax_class_id'];
        $taxClassess = $this->taxclass->getAllOptions();
        $gst = '0%';
        foreach ($taxClassess as $taxClass)
        {
            if($taxClass['value'] == $taxClassId && strpos($taxClass['label'],'@'))
            {
                $gst = substr($taxClass['label'], strrpos($taxClass['label'], '@' )+1);
            }
        }

        /* hsn code */
        $hsnCode = "";
        if($productHsnCode)
        {
            $hsnCode = substr($productHsnCode, 0, 4);
        }else{
            $hsnCode = "";
        }
        /* hsn code */

        $len = strlen($item->getName());
        $productName = $item->getName();
        if($len > 65){
            $productName =  substr($item->getName(),0,70);
        }
        // draw name
        $lines[0] = [['text' => $this->string->split($productName, 25, true, true), 'feed' => 28]];
        // draw SKU
        $lines[0][] = [
            'text' => $this->string->split($this->getSku($item), 20),
            'feed' => 240,
            'align' => 'right',
        ];


        // draw HSN Code
        $lines[0][] = [
            'text' => $this->string->split($hsnCode, 15),
            'feed' => 290,
            'align' => 'right',
        ];

        // draw GST Rate
        $lines[0][] = [
            'text' => $this->string->split($gst, 15),
            'feed' => 340,
            'align' => 'right',
        ];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 375, 'align' => 'right'];

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 450;
        $feedDisc = $feedPrice + 28;
        $feedSubtotal = $feedDisc + 80;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'right'];
                // draw Disc%
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedDisc, 'align' => 'right'];
                // draw Subtotal label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right'];
                $i++;
            }

            // draw Price
            $lines[$i][] = [
                'text' => $price,
                'feed' => $feedPrice,
                'font' => 'bold',
                'align' => 'right',
            ];
            // draw Disc%
            $lines[$i][] = [
                'text' => $discountAmount,
                'feed' => $feedDisc,
                'font' => 'bold',
                'align' => 'right',
            ];
            // draw Amount
            $lines[$i][] = [
                'text' => $priceData['subtotal'],
                'feed' => $feedSubtotal,
                'font' => 'bold',
                'align' => 'right',
            ];
            $i++;
        }

        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
