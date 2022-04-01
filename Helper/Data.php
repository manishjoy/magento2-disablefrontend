<?php
/**
 * Copyright � 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ManishJoy\DisableFrontend\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    // protected $_storeManager;
    // protected $_filesystem;
    // protected $orderFactory;

    // public function __construct(
    //     \Magento\Store\Model\StoreManagerInterface $storeManager,
    //     \Magento\Framework\Filesystem $filesystem,
    //     \Magento\Sales\Model\OrderFactory $orderFactory,
	// 	\Magento\Framework\App\Helper\Context $context
    // ) {
    //     $this->_storeManager = $storeManager;
    //     $this->_filesystem = $filesystem;
    //     $this->orderFactory = $orderFactory;
	// 	parent::__construct($context);
    // }

	public function getStoreConfigValue($sbsolutePath)
    {
        try {
			return $this->scopeConfig->getValue('dfrontend/settings/'.$sbsolutePath, ScopeInterface::SCOPE_STORE);
        } catch (\Exception $e) {
            return null;
        }
    }
}
