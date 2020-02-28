<?php

namespace G1Team\Theme\Block\Product\Widget;

use Magento\Catalog\Block\Product\Widget\NewWidget;

class ProductList extends NewWidget
{
    const DEFAULT_TITLE = 'Kien Custom Widget';

    const DEFAULT_PRODUCTS_COUNT = 4;

    const DEFAULT_TEMPLATE_KIEN = 'product/widget/slider1.phtml';

    const DEFAULT_PRODUCTS_PER_PAGE = 1;
    const DEFAULT_TYPE = 'slider';
    const DISPLAY_TYPE = 'list';

    protected $_imageHelperFactory;
    /**
     * TODO: ORVERWRITE
     *public function __construct(
     *    \Magento\Catalog\Block\Product\Context $context,
     *    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
     *    \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
     *    \Magento\Framework\App\Http\Context $httpContext,
     *    array $data = [],
     *    \Magento\Framework\Serialize\Serializer\Json $serializer = null
     *) {
     *    parent::__construct(
     *        $context,
     *        $productCollectionFactory,
     *        $catalogProductVisibility,
     *        $httpContext,
     *        $data,
     *        $serializer
     *    );
     *}
     *
     *public function getArray()
     *{
     *    $productId = [];
     *    $newproductList = $this->newproductRepository->getList();
     *    foreach ($newproductList as $newproduct) {
     *        array_push($productId, $newproduct->getProductid());
     *    }
     *    return $productId;
     *}
     *protected function _getProductCollection()
     *{
     *    if ($this->getShow() == 'product/widget/slider1.phtml') {
     *        # code...
     *        $collection = $this->_productCollectionFactory->create()
     *            ->addFieldToFilter('entity_id', ['in' => $this->getArray()]);
     *        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
     *        $collection = $this->_addProductAttributesAndPrices($collection)
     *            ->addStoreFilter()
     *            ->addAttributeToSort('created_at', 'desc')
     *            ->setPageSize($this->getProductsCount());
     *        return $collection;
     *    }
     *    $collection = $this->_productCollectionFactory->create()
     *        ->addFieldToFilter('entity_id', ['in' => $this->getArray()]);
     *    $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
     *    $collection = $this->_addProductAttributesAndPrices($collection)
     *        ->addStoreFilter()
     *        ->addAttributeToSort('created_at', 'desc')
     *        ->setPageSize($this->getPageSize())
     *        ->setCurPage($this->getCurrentPage());
     *    return $collection;
     *}
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Catalog\Helper\ImageFactory $imageFactory
    ) {
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $data,
            $serializer
        );
        $this->_imageHelperFactory = $imageFactory;
    }

    
     protected function _getProductCollection()
    {   
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);

        switch ($this->getType()) {
            case self::DISPLAY_TYPE:
                $collection->addStoreFilter()
                            ->addAttributeToSort('created_at', 'desc')
                            ->setPageSize($this->getPageSize());
                break;
            default:
            $collection->addStoreFilter()
                        ->addAttributeToSort('created_at', 'asc')
                        ->setPageSize($this->getPageSize());
                break;
        }
        // $collection = $this->_productCollectionFactory->create();
        // $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        // $collection = $this->_addProductAttributesAndPrices($collection)
        //     ->addStoreFilter()
        //     ->addAttributeToSort('created_at', 'asc')
        //     ->setPageSize($this->getPageSize());
        return $collection;
    }

    public function getImage_Url($product){
        $imageHelper = $this->_imageHelperFactory->create();
       return $imageHelper->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())->resize(1920, 1000)->getUrl();
    }

    public function getShow()
    {
        if (!$this->hasData('template')) {
            $this->setData('template', self::DEFAULT_TEMPLATE_KIEN);
        }
        return $this->getData('template');
    }

    public function getTitle()
    {
        if (!$this->hasData('title')) {
            $this->setData('title', self::DEFAULT_TITLE);
        }
        return $this->getData('title');
    }

    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return $this->getData('products_per_page');
    }
    public function getType()
    {
        if (!$this->hasData('type')) {
            $this->setData('type', self::DEFAULT_TYPE);
        }
        return $this->getData('type');
    }

    /**
     * public function getDisplayType()
     * {
     *     if (!$this->hasData('display_type')) {
     *         $this->setData('display_type', self::DISPLAY_TYPE_NEW_PRODUCTS);
     *     }
     *     return $this->getData('display_type');
     *}
     */
}
