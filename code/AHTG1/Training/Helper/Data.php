<?php

namespace AHTG1\Training\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

	const XML_PATH_HELLOWORLD = 'g1t566/'; // đây là section
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

	public function getGeneralConfig($code, $storeId = null)
	{

		return $this->getConfigValue(self::XML_PATH_HELLOWORLD .'general/'. $code, $storeId);
	}
    public function getBlock($identifier){
        $valueContent = $this->getLayout()
            ->createBlock('Magento\Cms\Block\Block')
            ->setBlockId($identifier)
			->toHtml();
		return $valueContent;
    }
} 
// muốn gọi trong block ta truyền vào class Data trong contruct. sau đó gọi như ví dụ sau getGeneralConfig('field's id'); 
// hoặc gọi ở template. Khai báo $myhelper = $this->helper('AHTG1\Training\Helper\Data'); 
                    //=> và gọi $myhelper->getGeneralConfig('yes');