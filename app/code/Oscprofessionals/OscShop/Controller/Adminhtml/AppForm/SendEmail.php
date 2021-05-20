<?php
/**
 * Copyright © 2016 Oscprofessionals® All Rights Reserved.
 */
namespace Oscprofessionals\OscShop\Controller\Adminhtml\AppForm;

use Magento\Backend\App\Action;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * Class SendEmail.
 */
class SendEmail extends Action
{
    /**
     * Config Path for email template.
     *
     * @const XML_PATH_OSCP_EMAIL_EMAIL_TEMPLATE
     */
    const XML_PATH_OSCP_EMAIL_EMAIL_TEMPLATE = 'oscshop/email/email_template';

    /**
     * First Recipient email address value.
     *
     * @const EMAIL_RECIPIENT_FIRST
     */
    const EMAIL_RECIPIENT_FIRST = 'pawan@oscprofessionals.com';

    /**
     * Second Recipient email address value.
     *
     * @const EMAIL_RECIPIENT_SECOND
     */
    const EMAIL_RECIPIENT_SECOND = 'praful@oscprofessionals.com';

    /**
     * Transport Builder.
     *
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * Inline Translation.
     *
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * Config Details.
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Store Manager.
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Escaper.
     *
     * @var Escaper
     */
    private $escaper;

    /**
     * Data Object Factory
     *
     * @var \Magento\Framework\DataObject\Factory
     */
    private $dataObjectFactory;

    /**
     * @param Action\Context        $context
     * @param TransportBuilder      $transportBuilder
     * @param StateInterface        $inlineTranslation
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Escaper               $escaper
     * @param DataObjectFactory     $dataObjectFactory
     */
    public function __construct(
        Action\Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Escaper $escaper,
        DataObjectFactory $dataObjectFactory
    ) {
    
        parent::__construct($context);

        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Send Email Action.
     *
     * @return void
     */
    public function execute()
    {
        $post = $this->getRequest()->getParams();

        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }

        $this->inlineTranslation->suspend();
        try {
            $postObject = $this->dataObjectFactory->create();
            $postObject->setData($post);

            $sender = [
                'name' => $this->_escaper->escapeHtml($post['user_name']),
                'email' => $this->_escaper->escapeHtml($post['email']),
            ];

            $storeScope = ScopeInterface::SCOPE_STORE;
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_OSCP_EMAIL_EMAIL_TEMPLATE
                    )
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_ADMINHTML,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->addTo(self::EMAIL_RECIPIENT_FIRST, $storeScope)
                ->addCc(self::EMAIL_RECIPIENT_SECOND, $storeScope)
                ->getTransport();

            /** @var $transport TransportInterface */
            $transport->sendMessage();

            $this->inlineTranslation->resume();

            $this->messageManager->addSuccess(
                __(
                    'Thanks for contacting us.'
                    . ' We\'ll respond to you very soon.'
                )
            );

            /** @noinspection PhpUndefinedMethodInspection */
            $this->_session->setData($post);

            $this->_redirect('*/*/senddata');
            return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();

            $this->messageManager->addError(
                __(
                    'We can\'t process your request right now.'
                    . 'Sorry, that\'s all we know.' . $e->getMessage()
                )
            );

            $this->_redirect('*/*/');
            return;
        }
    }
}
