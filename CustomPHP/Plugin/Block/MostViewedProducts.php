<?php

namespace Convert\Catalog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Magento\Catalog\Model\CategoryFactory;

/**
 * Class MostViewedProducts
 *
 * @package Convert\Catalog\Block
 */
class MostViewedProducts extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $_productsFactory;

    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @var ProductFactory
     */
    protected $_productLoader;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * MostViewedProducts constructor.
     *
     * @param Context $context
     * @param CollectionFactory $productsFactory
     * @param Image $imageHelper
     * @param ProductFactory $productLoader
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productsFactory,
        Image $imageHelper,
        ProductFactory $productLoader,
        Registry  $registry,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_productsFactory = $productsFactory;
        $this->_imageHelper = $imageHelper;
        $this->_productLoader = $productLoader;
        $this->_categoryFactory = $categoryFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Getting most viewed products
     */
    public function getCollection()
    {
        try {
            $currentStoreId = $this->_storeManager->getStore()->getId();
        } catch (\Exception $exception) {
            $currentStoreId = 1;
        }
        $currentCategoryId = $this->getCurrentCategory()->getId();
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->_categoryFactory->create()->load($currentCategoryId);
        /** @var \Magento\Reports\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->_productsFactory->create();
        $collection->addAttributeToSelect('*')
            ->addViewsCount()
            ->setStoreId($currentStoreId)
            ->addStoreFilter($currentStoreId)
            ->addCategoryFilter($category);
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
     * @param $productId
     * @return mixed
     */
    public function getProductThumbnailUrl($productId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_productLoader->create()->load($productId);
        return $this->_imageHelper->init($product, 'wishlist_thumbnail')->getUrl();
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory()
    {        
        return $this->_coreRegistry->registry('current_category');
    }
}