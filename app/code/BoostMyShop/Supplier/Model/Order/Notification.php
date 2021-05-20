<?php

namespace BoostMyShop\Supplier\Model\Order;

class Notification
{
    protected $_config;
    protected $_transportBuilder;
    protected $_storeManager;
    protected $_state;

    public function __construct(
        \BoostMyShop\Supplier\Model\Config $config,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_config = $config;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_state = $state;
    }

    public function notifyToSupplier($purchaseOrder)
    {

        $email = $purchaseOrder->getSupplier()->getsup_email();
        $name = $purchaseOrder->getSupplier()->getsup_contact();
        $storeId = ($purchaseOrder->getpo_store_id() ? $purchaseOrder->getpo_store_id() : 1);
        if (!$email)
            throw new \Exception('No email configured for this supplier');

        $template = $this->_config->getSetting('order/email_template', $storeId);
        $sender = $this->_config->getSetting('order/email_identity', $storeId);

        $params = $this->buildParams($purchaseOrder);

        $this->_sendEmailTemplate($template, $sender, $params, $storeId, $email, $name);
    }

    protected function _sendEmailTemplate($template, $sender, $templateParams = [], $storeId, $recipientEmail, $recipientName)
    {
        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            $sender
        )->addTo(
            $recipientEmail,
            $recipientName
        )->getTransport();
        $transport->sendMessage();

        return $this;
    }

    protected function buildParams($purchaseOrder)
    {
        $datas = [];

        foreach($purchaseOrder->getData() as $k => $v)
            $datas[$k] = $v;

        foreach($purchaseOrder->getSupplier()->getData() as $k => $v)
            $datas[$k] = $v;

        $datas['manager_fullname'] = $purchaseOrder->getManager()->getName();
        $datas['delivery_address'] = $this->_config->getSetting('pdf/billing_address', $purchaseOrder->getpo_store_id());
        $datas['shipping_address'] = $this->_config->getSetting('pdf/shipping_address', $purchaseOrder->getpo_store_id());
        $datas['company_name'] = $this->_config->getGlobalSetting('general/store_information/name', $purchaseOrder->getpo_store_id());

        $datas['order'] = $purchaseOrder;
        $datas['supplier'] = $purchaseOrder->getSupplier();

        $datas['pdf_url'] = $this->getDownloadPdfUrl($purchaseOrder);

        return $datas;
    }

    protected function getDownloadPdfUrl($purchaseOrder)
    {
        //getUrl from admi doesnt work, dirty workaround below (git issue : https://github.com/magento/magento2/issues/5322)
        $url = $this->_storeManager->getStore($purchaseOrder->getpo_store_id())->getUrl('supplier/po/download', ['_area' => 'frontend', 'po_id' => $purchaseOrder->getId(),  'token' => $purchaseOrder->getToken(), '_nosid' => 1]);
        $url = $this->_storeManager->getStore($purchaseOrder->getpo_store_id())->getBaseUrl().'supplier/po/download/po_id/'.$purchaseOrder->getId().'/token/'.$purchaseOrder->getToken();
        return $url;
    }

}