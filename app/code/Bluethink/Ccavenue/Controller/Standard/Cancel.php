<?php

namespace Bluethink\Ccavenue\Controller\Standard;

class Cancel extends \Bluethink\Ccavenue\Controller\CcavenueAbstract {

    public function execute() {
        $this->getOrder()->cancel()->save();
        
        $this->messageManager->addErrorMessage(__('Your order has been can cancelled'));
        $this->getResponse()->setRedirect(
                $this->getCheckoutHelper()->getUrl('checkout')
        );
    }

}
