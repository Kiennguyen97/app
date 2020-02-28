<?php

namespace G1Team\Theme\Block\Product\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;

class SubHead extends AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
    const DEFAULT_TITLE = 'KienSubHead';

    const DEFAULT_PRODUCTS_ID = 6;

    const DEFAULT_TEMPLATE_KIEN = 'product/widget/Subhead.phtml';

    // const DEFAULT_PRODUCTS_PER_PAGE = 1;
    protected $_imageHelperFactory;
    protected $_productRepository;
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
        \Magento\Catalog\Helper\ImageFactory $imageFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        array $data = []
    ) {
        $this->_productRepository = $productRepositoryInterface;
        $this->_imageHelperFactory = $imageFactory;
        parent::__construct(
            $context,
            $data
        );
    }


    public function getProduct_byID($id)
    {   
        $product =  $this->_productRepository->getById($id);
        return $product;
    }

    public function getImage_Url($product)
    {
        $imageHelper = $this->_imageHelperFactory->create();
        return $imageHelper->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())->resize(454, 555)->getUrl();
    }


    public function getProductID()
    {
        if (!$this->hasData('product_id')) {
            $this->setData('product_id', self::DEFAULT_PRODUCTS_ID);
        }
        return $this->getData('product_id');
    }

    
}
