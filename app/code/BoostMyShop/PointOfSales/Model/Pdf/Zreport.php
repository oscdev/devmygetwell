<?php

namespace BoostMyShop\PointOfSales\Model\Pdf;


class Zreport extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    protected $_storeManager;
    protected $_localeResolver;
    protected $_orderFactory;
    protected $_config;
    protected $_managerFactory;
    protected $_dateTime;
    protected $_sectionMargin = 15;
    protected $_resourceModel;
    protected $_content;
    protected $_posRegistry;
    protected $_openingFactory;

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
        \BoostMyShop\PointOfSales\Model\Registry $posRegistry,
        \BoostMyShop\PointOfSales\Model\OpeningFactory $openingFactory,
        \BoostMyShop\PointOfSales\Model\ResourceModel\Zreport $resourceModel,
        \BoostMyShop\PointOfSales\Model\Order\ManagerFactory $managerFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_orderFactory = $orderFactory;
        $this->_config = $config;
        $this->_managerFactory = $managerFactory;
        $this->_dateTime = $dateTime;
        $this->_resourceModel = $resourceModel;
        $this->_posRegistry = $posRegistry;
        $this->_openingFactory = $openingFactory;

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
    public function getPdf($settings = [])
    {
        $this->_beforeGetPdf();

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        $this->_content = $this->getContent($settings);

        $page = $this->newPage();
        $this->drawContent($page, $this->_content);

        $this->_afterGetPdf();
        return $pdf;
    }

    public function getContent($settings)
    {
        $content = [];

        $section = [];
        $section[] = ['title' => 'Report date', 'value' => $this->_dateTime->gmtDate()];
        $section[] = ['title' => 'Register', 'value' => $settings['register']];
        $section[] = ['title' => 'Start date', 'value' => $settings['from']];
        $section[] = ['title' => 'Closing date', 'value' => $settings['to']];
        $content[] = $section;

        $section = [];
        $openingTotal = number_format($this->getOpeningTotal($settings), 2, '.', ' ');
        $section[] = ['title' => 'Opening total ('.$this->_config->getCashDrawerMethod().')', 'value' => $openingTotal];
        $itemSales = number_format($this->_resourceModel->getTotalSales($settings), 2, '.', ' ');
        $section[] = ['title' => 'Item sales', 'value' => $itemSales];
        $totalReturn = number_format($this->_resourceModel->getTotalReturn($settings), 2, '.', ' ');
        $section[] = ['title' => 'Item Returns', 'value' => $totalReturn];
        $taxes = number_format($this->_resourceModel->getTotalTax($settings), 2, '.', ' ');
        $section[] = ['title' => 'Tax', 'value' => $taxes];
        $total = number_format($openingTotal + $itemSales + $totalReturn + $taxes, 2, '.', ' ');
        $section[] = ['title' => 'Total', 'value' => $total];
        $content[] = $section;

        //taxes
        $section = [];
        $section[] = ['title' => 'Tax collected :', 'value' => ''];
        foreach($this->_resourceModel->getTaxAmountPerPercent($settings) as $item)
        {
            $section[] = ['title' => $item['tax_percent'].'%', 'value' => $item['total']];
        }
        $content[] = $section;

        //payment methods totals
        $section = [];
        $section[] = ['title' => 'Total per method :', 'value' => ''];
        $paymentsPerMethod = $this->_resourceModel->getPaymentsPerMethods($settings);
        foreach($paymentsPerMethod as $item)
        {
            $section[] = ['title' => $item['method'], 'value' => $item['total']];
        }
        $content[] = $section;

        //payment methods count
        $section = [];
        $section[] = ['title' => 'Transactions per method :', 'value' => ''];
        foreach($this->_resourceModel->getTransactionsPerMethods($settings) as $item)
        {
            $section[] = ['title' => $item['method'], 'value' => $item['total']];
        }
        $content[] = $section;

        //list of sales
        $section = [];
        $section[] = ['title' => 'Sales :', 'value' => ''];
        foreach($this->_resourceModel->getSales($settings) as $item)
        {
            $section[] = ['title' => $item['increment_id'].' : '.$item['method'], 'value' => number_format($item['amount'] ? $item['amount'] : $item['grand_total'], 2, '.', ' ')];
        }
        $content[] = $section;

        $section = [];
        $section[] = ['title' => 'Cash drawer expected', 'value' => $this->getCashDrawerExpected($settings, $paymentsPerMethod)];
        $content[] = $section;

        return $content;
    }


    protected function getOpeningTotal($settings)
    {
        list($settings['from'], $fake) = explode(" ", $settings['from']);
        list($settings['to'], $fake) = explode(" ", $settings['to']);

        if ($settings['from'] == $settings['to'])
        {
            $storeId = $this->_posRegistry->getCurrentStoreId();
            $date = $settings['from'];
            $opening = $this->_openingFactory->create()->loadByStoreDate($storeId, $date);
            return $opening->getpo_amount();
        }
        else
            return 0;
    }

    public function drawContent($page, $content)
    {
        foreach($content as $section)
        {
            $lines = [];
            foreach($section as $item)
            {
                $line = [];
                $line[] = ['text' => $item['title'], 'feed' => 10, 'align' => 'left'];
                $line[] = ['text' => $item['value'], 'feed' => $this->getWidth() -10, 'align' => 'right'];
                $lines[] = $line;
            }
            $lineBlock = ['lines' => $lines, 'height' => 12];
            $this->drawLineBlocks($page, [$lineBlock]);
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.6));
            $page->drawLine(5, $this->y + 5, $this->getWidth() - 10, $this->y + 5);
            $this->y -= $this->_sectionMargin;
        }
    }

    public function getWidth()
    {
        return ((($this->_config->getReceiptWidth()) / 2.54) * 595) / 8.26;
    }

    public function getHeight()
    {
        $height = 20 + $this->_sectionMargin * 4;

        foreach($this->_content as $section)
        {
            $height += $this->_sectionMargin;
            foreach($section as $item)
            {
                $height += $this->_sectionMargin;
            }
        }

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

        $headerText = 'Z Report';
        $textWidth = $this->widthForStringUsingFontSize($headerText, $this->getFontRegular(), 12);
        $x = ($this->getWidth() - $textWidth) / 2;
        $page->drawText($headerText, $x, $this->y - 20, 'UTF-8');
        $this->y -= 15;

        $this->y -= $this->_sectionMargin * 4;

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

    public function getCashDrawerExpected($settings, $paymentsPerMethod)
    {
        $value = 0;

        $value += $this->getOpeningTotal($settings);

        foreach($paymentsPerMethod as $method)
        {
            if ($method['method'] == $this->_config->getCashDrawerMethod())
                $value += $method['total'];
        }


        return number_format($value, 2, '.', ' ');
    }
}
