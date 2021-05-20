<?php

namespace BoostMyShop\PointOfSales\Model\Pdf;


class Receipt extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    protected $_storeManager;
    protected $_localeResolver;
    protected $_orderFactory;
    protected $_config;
    protected $_managerFactory;
    protected $_paymentCollectionFactory;

    protected $_sectionMargin = 30;

    protected $_currentOrder;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \BoostMyShop\PointOfSales\Model\Config $config,
        \BoostMyShop\PointOfSales\Model\Order\ManagerFactory $managerFactory,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Payment\CollectionFactory $paymentCollectionFactory,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_orderFactory = $orderFactory;
        $this->_config = $config;
        $this->_managerFactory = $managerFactory;
        $this->_paymentCollectionFactory = $paymentCollectionFactory;

        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );

    }


    /**
     * Return PDF document
     *
     * @param array|Collection
     * @return \Zend_Pdf
     */
    public function getPdf($orders = [])
    {
        $this->_beforeGetPdf();

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 8);

        foreach ($orders as $order) {

            $this->_currentOrder = $order;

            if ($order->getStoreId()) {
                $this->_localeResolver->emulate($order->getstore_id());
                $this->_storeManager->setCurrentStore($order->getstore_id());
            }
            $page = $this->newPage();

            $this->_drawOrderInformation($page, $order);
            $this->_drawProductHeader($page);
            foreach ($order->getAllItems() as $item) {
                $this->_drawProduct($item, $page, $order);
                $page = end($pdf->pages);
            }
            $this->y -= $this->_sectionMargin;

            $this->_drawTotals($page, $order);
            $this->_drawPayments($page, $order);
            $this->_drawFooter($page);

            if ($order->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    public function getWidth()
    {
        return ((($this->_config->getReceiptWidth()) / 2.54) * 595) / 8.26;
    }

    public function getHeight()
    {
        $height = 0;

        //header
        $headerText = $this->_config->getReceiptHeaderText();
        $headerText = $this->splitTextForLineWeight($headerText, $this->getWidth() - 20, $this->getFontRegular(), 24);
        $height += count($headerText) * 15;
        $height += $this->_sectionMargin;

        //order information
        $height += 3 * 15;
        $height += $this->_sectionMargin;

        //products
        $height += (count($this->_currentOrder->getAllItems()) + 1) * 15;
        $height += $this->_sectionMargin;

        //totals
        $height += 4 * 15;
        $height += $this->_sectionMargin;

        //payments
        $payments = $this->_paymentCollectionFactory->create()->addOrderFilter($this->_currentOrder->getId());
        $height += 15 + 15 * count($payments);

        //footer
        $headerText = $this->_config->getReceiptFooterText();
        $headerText = $this->splitTextForLineWeight($headerText, $this->getWidth() - 20, $this->getFontRegular(), 12);
        $height += count($headerText) * 15;
        $height += $this->_sectionMargin;

        return $height;
    }

    public function newPage(array $settings = [])
    {
        $size = $this->getWidth(). ':' . $this->getHeight().':';
        $page = $this->_getPdf()->newPage($size);
        $this->_getPdf()->pages[] = $page;
        $this->y = $this->getHeight();
        $this->_drawHeader($page);
        return $page;
    }


    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        $this->_setFontBold($page, 12);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

        $headerText = $this->_config->getReceiptHeaderText();
        $headerText = $this->splitTextForLineWeight($headerText, $this->getWidth() - 20, $this->getFontRegular(), 20);
        foreach($headerText as $line) {
            $txtWidth = $this->widthForStringUsingFontSize($line, $this->getFontRegular(), 12);
            $x = ($this->getWidth() - $txtWidth) / 2;
            $page->drawText($line, $x, $this->y - 20, 'UTF-8');
            $this->y -= 15;
        }

        $this->y -= $this->_sectionMargin;

    }

    protected function _drawOrderInformation(\Zend_Pdf_Page $page, $order)
    {
        $lines = [];
        $lines[] = 'Order #'.$order->getincrement_id();
        $lines[] = 'Date : '.$order->getCreatedAtFormatted(2);
        $lines[] = 'Seller : '.$this->_managerFactory->create()->loadByOrderId($order->getId())->getUser()->getName();

        $this->setFontRegular($page, 8);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

        foreach($lines as $line) {
            $page->drawText($line, 10, $this->y - 20, 'UTF-8');
            $this->y -= 15;
        }

        $this->y -= $this->_sectionMargin;

    }


    protected function _drawProductHeader($page)
    {
        $this->_setFontBold($page, 8);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

        $lines[0][] = ['text' => 'Qty', 'feed' => 10, 'align' => 'left'];
        $lines[0][] = ['text' => 'Product', 'feed' => 40, 'align' => 'left'];
        $lines[0][] = ['text' => 'Price', 'feed' => $this->getWidth() - 20, 'align' => 'right'];
        $lineBlock = ['lines' => $lines, 'height' => 5];

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->drawLine(5, $this->y, $this->getWidth() - 10, $this->y);
        $this->y -= 15;


    }

    /**
     * @param $item
     * @param $page
     * @param $order
     */
    protected function _drawProduct($item, $page, $order)
    {
        /* Add table head */
        $this->_setFontRegular($page, 8);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

        //columns headers
        $lines[0][] = ['text' => (int)$item->getqty_ordered(), 'feed' => 10, 'align' => 'left'];
        $lines[0][] = ['text' => $item->getPosAliseName(), 'feed' => 25, 'align' => 'left'];
        
   
        $lines[0][] = ['text' => $order->formatPriceTxt($item->getPrice()), 'feed' => $this->getWidth() - 20, 'align' => 'right'];
       // $lines[1][] = ['text' => '', 'feed' => 10, 'align' => 'left'];
       //  $lines[2][] = ['text' => $item->getPosAliseName(), 'feed' => 10, 'align' => 'left','font_size'=>7];
        $lineBlock = ['lines' => $lines, 'height' => 5];

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 18;
    }


    protected function _drawTotals($page, $order)
    {
        $totals = [];
        $totals['Subtotal'] = $order->getSubtotal();
        $totals['Shipping'] = $order->getshipping_amount();
        $totals['GST'] = $order->gettax_amount();
        $totals['Total'] = $order->getGrandTotal();

        $lines = [];
        foreach($totals as $k => $v)
        {
            if ($v == 0)
                continue;
            $line = [];
            $line[] = ['text' => $k, 'feed' => $this->getWidth() - 140, 'align' => 'left', 'font' => 'LinLibertine_Bd-2.8.1.ttf', 'font_size' => 12];
            $line[] = ['text' => $order->formatPriceTxt($v), 'feed' => $this->getWidth() - 20, 'align' => 'right', 'font' => 'LinLibertine_Bd-2.8.1.ttf', 'font_size' => 12];
            $lines[] = $line;
        }
        $lineBlock = ['lines' => $lines, 'height' => 15];
        $this->drawLineBlocks($page, [$lineBlock]);

        $this->y -= $this->_sectionMargin;
    }

    protected function _drawPayments($page, $order)
    {
        $this->_setFontRegular($page, 12);

        $page->drawLine(5, $this->y, $this->getWidth() - 10, $this->y);
        $this->y -= 15;

        $payments = $this->_paymentCollectionFactory->create()->addOrderFilter($order->getId());
        $lines = [];
        foreach($payments as $payment)
        {
            $line = [];
            $line[] = ['text' => $payment->getMethod(), 'feed' => 10, 'align' => 'left', 'font' => 'LinLibertine_Re-4.4.1.ttf', 'font_size' => 12];
            $line[] = ['text' => $order->formatPriceTxt($payment->getAmount()), 'feed' => $this->getWidth() - 20, 'align' => 'right', 'font' => 'LinLibertine_Re-4.4.1.ttf', 'font_size' => 12];
            $lines[] = $line;

        }
        $lineBlock = ['lines' => $lines, 'height' => 15];
        $this->drawLineBlocks($page, [$lineBlock]);

    }

    protected function _drawFooter(\Zend_Pdf_Page $page)
    {
        $this->_setFontRegular($page, 12);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.3));

        $page->drawLine(5, $this->y, $this->getWidth() - 10, $this->y);

        $headerText = $this->_config->getReceiptFooterText();
        $headerText = $this->splitTextForLineWeight($headerText, $this->getWidth() - 20, $this->getFontRegular(), 12);
        foreach($headerText as $line) {
            $page->drawText($line, 10, $this->y - 20, 'UTF-8');
            $this->y -= 15;
        }

    }



    public function getPdfObject()
    {
        return $this->_getPdf();
    }

    public function setFontBold($page, $size)
    {
        $this->_setFontBold($page, $size);
        return $this;
    }

    public function setFontRegular($page, $size)
    {
        $this->_setFontRegular($page, $size);
        return $this;
    }

    protected function getFontRegular()
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_rootDirectory->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Re-4.4.1.ttf')
        );
        return $font;
    }
    
    
    public function splitTextForLineWeight($txt, $maxWidth, $font, $fontSize)
    {
        $lines = [];
        $words = explode(' ', $txt);
        $currentLine = '';
        foreach($words as $word)
        {
            if ($this->widthForStringUsingFontSize($currentLine.' '.$word, $font, $fontSize) > $maxWidth)
            {
                $lines[] = trim($currentLine);
                $currentLine = $word;
            }
            else
                $currentLine .= ' '.$word;
        }
        $lines[] = trim($currentLine);
        return $lines;
    }

}