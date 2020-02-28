<?php
namespace AHT\G1T563\Block;
// use Magento\Framework\App\Config\ScopeConfigInterface;
// use Magento\Framework\Event\Observer;


class Index extends \Magento\Framework\View\Element\Template 
{
        protected $scopeConfig;
        protected $productRepository;

        public function __construct(
                \Magento\Backend\Block\Template\Context $context,
                \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
            {
                $this->scopeConfig = $scopeConfig;
                $this->productRepository = $productRepositoryInterface;
                parent::__construct($context);
                
            }
        function _prepareLayout(){}
        // function getproductAttribute(){
        //         $product =  $this->productRepository->getById('22');
        //         return $product->getAttributes();
        // }
}
