<?php
namespace AHT\G1T563\Block;
use Magento\Framework\App\Config\ScopeConfigInterface;
// use Magento\Framework\Event\Observer;


class MyBlock extends \Magento\Framework\View\Element\Template 
{
    const XML_DEFAULT = 'helloworld/general/enable';
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        // if ($this->hasData('template')) {
        //     $this->setTemplate($this->getData('template'));
        // }
    }
    public function Check()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $config = $this->scopeConfig->getValue(self::XML_DEFAULT, $storeScope);
        if(!$config){
            return false;
        }
        return true;
        // function _prepareLayout(){}
    }
}
