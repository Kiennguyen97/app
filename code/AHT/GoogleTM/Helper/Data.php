<?php

namespace Bnkr\GoogleTM\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->layout = $layout;
		parent::__construct($context);
	}
	public function getConfigValue($field, $storeId = null)
	{
		return $this->scopeConfig->getValue(
			$field, ScopeInterface::SCOPE_STORE, $storeId
		);
	}
	
	public function getGoImageDefault( $storeId = null)
	{
		return $this->scopeConfig->getValue(
			'mpanel/goimage/image', ScopeInterface::SCOPE_STORE, $storeId
		);
	}
    
} 
