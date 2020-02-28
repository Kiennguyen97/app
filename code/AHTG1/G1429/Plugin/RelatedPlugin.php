<?php

namespace AHTG1\G1429\Plugin;

use Magento\Framework\Filter\TruncateFilter\Result;

class RelatedPlugin
{
    protected $categoryRepository;
    protected $_myHelper;
    protected $_categoryCollectionFactory;

    public function __construct(
        \AHTG1\G1429\Helper\Data $myHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->_myHelper = $myHelper;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;

    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function afterGetItems(
        \Magento\Catalog\Block\Product\ProductList\Related $subject,
        $result
    ) {
        if ($this->_myHelper->getGeneralConfig('yes')) {
            $relatedConfigtype = $this->_myHelper->getGeneralConfig('productshow');
            if ($relatedConfigtype == 1) {
                $notInThisCatId = $this->_myHelper->getGeneralConfig('productcate');
                $result1 = clone $result;
                foreach ($result1 as $product) {
                    $cats = $product->getCategoryIds();
                        if (in_array($notInThisCatId,$cats)) {
                            # code...
                            $result->removeItemByKey($product->getId());
                        }
                }
            }else if($relatedConfigtype == 2) {
                $notSku = $this->_myHelper->getGeneralConfig('productsku');
                $listsku = explode(";",$notSku);
                $result2 = clone $result;
                foreach ($result2 as $product) {
                    $sku = $product->getSku();
                        if (in_array($sku,$listsku)) {
                            # code...
                            $result->removeItemByKey($product->getId());
                        }
                }
            }
        }
        
        
        return $result;
    }
}
