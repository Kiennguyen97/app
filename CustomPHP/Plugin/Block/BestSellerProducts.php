<?php

namespace Convert\Catalog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class BestSellerProducts
 *
 * @package Convert\Catalog\Block
 */
class BestSellerProducts extends Template
{

    /**
     * @var BestSellersCollectionFactory 
     */
    protected $_bestSellersCollectionFactory;

    /**
     * @var ProductFactory 
     */
    protected $_productLoader;

    /**
     * @var CollectionFactory 
     */
    protected $_productCollectionFactory;

    /**
     * @var SearchCriteriaBuilder 
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface 
     */
    protected $_productRepositoryInterface;

    /**
     * BestSellerProducts constructor.
     *
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
     * @param ProductFactory $_productLoader
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        ProductFactory $_productLoader,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepositoryInterface,
        array $data = []
    ) {
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productLoader = $_productLoader;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        parent::__construct($context, $data);
    }


    /**
     * get collection of best-seller products
     *
     * @return mixed
     */
    public function getProductCollection()
    {
        $productIds = [];
        /** @var \Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection $bestSellers */
        $bestSellers = $this->_bestSellersCollectionFactory->create()->setPeriod('month');
        foreach ($bestSellers as $product) {
            $productIds[] = $product->getProductId();
        }
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                   ->addFieldToFilter('entity_id', array('in' => $productIds));
        return $collection;
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Model\Product
     */
    public function getLoadProduct($id)
    {
        return $this->_productLoader->create()->load($id);
    }

    /**
     * @param $brandLink
     * @return string
     */
    public function LinkStore($brandLink)
    {
        try {
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        } catch (\Exception $exception) {
            $baseUrl = '';
        }
        return $baseUrl.'brand/'.$brandLink.'.html';
    }
}