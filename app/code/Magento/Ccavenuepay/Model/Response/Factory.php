<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Ccavenuepay\Model\Response;

/**
 * Factory class for @see \Magento\Ccavenuepay\Model\Response
 */
class Factory {

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
    \Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = 'Magento\Ccavenuepay\Model\Response'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\Ccavenuepay\Model\Response
     */
    public function create(array $data = []) {
        return $this->objectManager->create($this->instanceName, $data);
    }

}
