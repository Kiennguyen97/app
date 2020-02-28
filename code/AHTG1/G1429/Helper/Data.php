<?php

namespace AHTG1\G1429\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
	protected $categoryRepository;
	const XML_PATH_SEARCH = 'g1429/'; // đây là section
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\View\LayoutInterface $layout,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository
    ) {
        $this->layout = $layout;
		parent::__construct($context);
		$this->categoryRepository = $categoryRepository;
	}
	public function getConfigValue($field, $storeId = null)
	{
		return $this->scopeConfig->getValue(
			$field, ScopeInterface::SCOPE_STORE, $storeId
		);
	}

	public function getGeneralConfig($code, $storeId = null)
	{

		return $this->getConfigValue(self::XML_PATH_SEARCH .'general/'. $code, $storeId);
	}
	public function getCategoryId($product){
		$cats = $product->getCategoryIds();
		return $cats;
	}
	public function checkProductbyCategory(array $cats,$id){
		foreach ($cats as $cat) {
			if ($id == $cat) {
				return true;
			}
		}
		return false;
	}
    
} 
// muốn gọi trong block ta truyền vào class Data trong contruct. sau đó gọi như ví dụ sau getGeneralConfig('field's id'); 
// hoặc gọi ở template. Khai báo $myhelper = $this->helper('AHTG1\G1429\Helper\Data'); 
                    //=> và gọi $myhelper->getGeneralConfig('yes');