<?php

namespace BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout;

class Refresh extends \BoostMyShop\PointOfSales\Controller\Adminhtml\Checkout
{
    protected $_lastPerfLogTimestamp = null;

    /**
     * @return void
     */
    public function execute()
    {
        $this->addPerfLog('start');

        $data = $this->getRequest()->getPostValue();
        $quoteId = (isset($data['quote_id']) ? $data['quote_id'] : null);
        $createOrder = (isset($data['create_order']) && ($data['create_order'] == 'true') ? true : false);
        $result = ['quote_id' => $quoteId];

        try
        {
            $this->_storeManager->setCurrentStore($this->_registry->getCurrentStoreId());

            if (!$quoteId)
            {
                $storeId = $this->_registry->getCurrentStoreId();
                $store = $this->_storeFactory->create()->load($storeId);
                $this->_quoteHelper->initQuote($store, '');
                $this->setGuestCustomer();
                $this->addPerfLog('after create quote');
            }
            else
            {
                $this->_quoteHelper->load($quoteId);
                $this->addPerfLog('after load quote');
            }

            //customer
            if (array_key_exists('customer', $data)) {
                if (isset($data['customer'])) {
                    switch ($data['customer']['mode']) {
                        case 'guest':
                            $this->setGuestCustomer();
                            break;
                        case 'customer':
                            $this->setCustomerData($data['customer'], $data['address']);
                            break;
                    }
                }
            }
            $this->addPerfLog('after set customer data');

            //products
            if (isset($data['products'])) {
                foreach ($data['products'] as $productId => $productData) {
                    $qty = $productData['qty'];
                    $shipLater = $productData['ship_later'];
                    $customPrice = (isset($productData['custom_price']) ? $productData['custom_price'] : '');
                    $this->_quoteHelper->addOrUpdateProduct($productId, $qty, $customPrice, $shipLater);
                }
            }
            $this->addPerfLog('after set products');

            //coupon
            if (isset($data['coupon_code']) && $data['coupon_code'])
                $this->_quoteHelper->applyCoupon($data['coupon_code']);
            else
                $this->_quoteHelper->applyCoupon('');
            $this->addPerfLog('after set coupon');

            //shipping method
            $shippingMethod = $this->_config->getDefaultShippingMethod();
            if (isset($data['shipping_method']))
                $shippingMethod = $data['shipping_method']['method'];
            if ($shippingMethod)
                $this->_quoteHelper->setShippingMethod($shippingMethod);
            $this->addPerfLog('after set shipping');

            //payment method
            $paymentMethod = $this->_config->getDefaultPaymentMethod();
            $paymentData = [];
            if (isset($data['payment_method']))
                $paymentMethod = $data['payment_method'];
            if (isset($data['payment']))
                $paymentData = $data['payment'];
            if ($paymentMethod)
                $this->_quoteHelper->setPaymentMethod($paymentMethod, $paymentData);
            $this->addPerfLog('after set payment');


            //
            if ($createOrder)
            {
                $order = $this->_quoteHelper->placeOrder();
                if (!$order)
                    throw new \Exception('Order creation failed');

                $order = $this->_orderFactory->create()->load($order->getId());
                $createInvoice = (isset($data['create_invoice']) && ($data['create_invoice'] == 'true') ? true : false);
                if ($createInvoice)
                    $this->_invoiceHelper->createInvoice($order);

                $order = $this->_orderFactory->create()->load($order->getId());     //prevent issue with advancedStock module that doesnt update qty to ship at order item level if we dont relead the order before creating the shipment
                $createShipment = (isset($data['create_shipment']) && ($data['create_shipment'] == 'true') ? true : false);
                if ($createShipment) {
                    $this->_shipmentHelper->createShipment($order, $this->_quoteHelper->getProductIdsToShip());
                }

                //store manager
                $userId = $this->_registry->getCurrentUserId();
                $this->_orderManagerFactory->create()->assignManagerToOrder($order->getId(), $userId);

                $result['success'] = true;
                $result['order_id'] = $order->getId();
                $result['action'] = $this->getUrl('pointofsales/sales/view', ['id' => $order->getId(), 'download_receipt' => 1]);
                $result['action_label'] = 'Order #'.$order->getIncrementId();
                $result['msg'] = 'New Order created : '.$order->getIncrementId();
            }
            else
            {
                $result = $this->_quoteHelper->getResult();
                $this->addPerfLog('after get results');

                $this->hydrateResultWithProductDetails($result);
                $this->addPerfLog('after hydrate products');

                $user = $this->getUser();
                $result['user']['id'] = $user->getId();
                $result['user']['name'] = $user->getName();
            }

            $result['success'] = true;
        }
        catch(\Exception $ex)
        {
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
            $result['stack'] = $ex->getTraceAsString();
        }


        die(json_encode($result));
    }

    protected function hydrateResultWithProductDetails(&$result)
    {
        foreach($result['items'] as &$item)
        {
            $item['image_url'] = $this->getImage($item['product_id']);
        }
    }

    protected function getImage($productId)
    {
        $product = $this->_productFactory->create()->load($productId);
        return $this->_productHelper->getImageUrl($product);
    }

    protected function setGuestCustomer()
    {
        $email = $this->_config->getGuestField('email');

        $this->_quoteHelper->setGuestCustomer($email, $this->getGuestAddress());
    }

    protected function setCustomerData($customerData, $address)
    {
        $customer= $this->_customerRepository->getById($customerData['entity_id']);
        $this->_quoteHelper->setCustomer($customer);

        $hasAddress = false;
        foreach($address as $k => $v)
        {
            if ($v)
                $hasAddress = true;
        }

        if (!$hasAddress)
            $address = $this->getGuestAddress();

        $this->_quoteHelper->setAddress($address);

        return $this;
    }

    protected function getGuestAddress()
    {

        //$country = $this->_config->getGuestField('country');
        $country = $this->_config->getGuestField('country_id');
        if (!$country)
            // $country = $this->_config->getGlobalSetting('general/country/default');
            $country = $this->_config->getGlobalSetting('general/country_id/default');

        $firstname = $this->_config->getGuestField('firstname');
        $lastname = $this->_config->getGuestField('lastname');
        $region = $this->_config->getGuestField('region_id');
        $street = $this->_config->getGuestField('street');
        $city = $this->_config->getGuestField('city');
        $phone = $this->_config->getGuestField('telephone');
        $zip = $this->_config->getGuestField('postcode');

        $data = [
            'firstname'    => $firstname,
            'lastname'     => $lastname,
            'street' => $street,
            'city' => $city,
            'country_id' => $country,
            'region_id' => $region,
            'postcode' => $zip,
            'telephone' => $phone,
            'save_in_address_book' => 0
        ];
        return $data;
    }

    public function getUser()
    {
        $userId = $this->_registry->getCurrentUserId();
        $user = $this->_userFactory->create()->load($userId);
        return $user;
    }

    protected function addPerfLog($msg)
    {
        if ($this->_lastPerfLogTimestamp)
        {
            $duration = microtime(true) - $this->_lastPerfLogTimestamp;
            $msg = $duration." - ".$msg;
        }

        $this->_lastPerfLogTimestamp = microtime(true);

        $this->_logger->log($msg, \BoostMyShop\PointOfSales\Helper\Logger::kLogPerf);
    }
}
