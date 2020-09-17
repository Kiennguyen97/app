<?php

namespace Convert\Catalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Request\Http;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class Data
 *
 * @package Convert\Catalog\Helper'
 */
class Data extends AbstractHelper
{
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CurrencyFactory
     */
	protected $_currencyFactory;

    /**
     * @var Http
     */
	protected $_http;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var CollectionFactory
     */
	protected $_wishListCollectionFactory;

    /**
     * @var Registry
     */
	protected $_registry;

    /**
     * @var HttpContext
     */
	protected $_httpContext;


    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeConfig
     * @param CurrencyFactory $currencyFactory
     * @param Http $request
     * @param Session $session
     * @param ProductRepositoryInterface $productRepository
     * @param CollectionFactory $wishListCollectionFactory
     * @param Registry $registry
     * @param HttpContext $httpContext
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
		StoreManagerInterface $storeConfig,
		CurrencyFactory $currencyFactory,
        Http $request,
        Session $session,
		ProductRepositoryInterface $productRepository,
        CollectionFactory $wishListCollectionFactory,
        Registry $registry,
        HttpContext $httpContext
    ) {
        parent::__construct($context);
        $this->_productFactory = $productFactory;
		$this->_storeManager = $storeConfig;
		$this->_currencyFactory = $currencyFactory;
		$this->_http = $request;
        $this->_customerSession = $session;
        $this->_request = $request;
        $this->_productRepository = $productRepository;
		$this->_wishListCollectionFactory = $wishListCollectionFactory;
		$this->_registry = $registry;
        $this->_httpContext = $httpContext;
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Model\Product
     */
    public function getLoadProduct($id)
    {
        return $this->_productFactory->create()->load($id);
    }

    /**
     * @return mixed
     */
	public function getCurrentCat()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * @return mixed
     */
    public function getCustomerSessionLogin()
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->_customerSession->getCustomer()->getGroupId();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
	public function getSymbol()
    {
        $currentCurrency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        /** @var \Magento\Directory\Model\Currency $currencyMode */
        $currencyMode = $this->_currencyFactory->create();
        $currency = $currencyMode->load($currentCurrency);
        return $currency->getCurrencySymbol();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
	public function getMediaUrl() 
	{
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
	public function getMediaGalleryImages($product)
    {
        try {
            $_product = $this->_productRepository->get($product->getSku(), false, null, true);
            return $_product->getMediaGalleryImages();
        } catch (\Exception $exception) {
            return null;
        }
    }
}