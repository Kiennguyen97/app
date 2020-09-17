<?php
namespace Bnkr\GoogleTM\Block;
class GoogleTM extends \Magento\Framework\View\Element\Template
{
        protected $_storeManager;
        protected $_urlInterface;
        protected $storeRepository;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\UrlInterface $urlInterface,    
        array $data = []
    )
    {        
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->storeRepository = $storeRepository;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    /**
     * Prining URLs using StoreManagerInterface
     */
    public function getStoreManagerData()
    {    
        echo $this->_storeManager->getStore()->getId() . '<br />';
        
        // by default: URL_TYPE_LINK is returned
        echo $this->_storeManager->getStore()->getBaseUrl() . '<br />';        
        
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_DIRECT_LINK) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . '<br />';
        echo $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC) . '<br />';
        
        echo $this->_storeManager->getStore()->getUrl('product/33') . '<br />';
        
        echo $this->_storeManager->getStore()->getCurrentUrl(false) . '<br />';
        echo $this->_storeManager->getStore()->getCurrentUrl(true) . '<br />';

            
        echo $this->_storeManager->getStore()->getBaseMediaDir() . '<br />';
            
        echo $this->_storeManager->getStore()->getBaseStaticDir() . '<br />';    
    }

    public function getCurrentStoreID(){
        return $this->_storeManager->getStore()->getId();
    }

    public function getStores(){
        // return $this->_storeManager->getStores();
        $stores = $this->storeRepository->getList();
        // foreach ($stores as $store) {
        //     echo 'Store: ' . $store->getBaseUrl();
        //     echo 'code:'.$store->getId().' --- ';
        // }
        return $stores;
    }


    public function getStoreUrl ($store_id = null){
        $store = $this->storeRepository->getById($store_id);
        return $store->getBaseUrl();
    }

    /**
     * Prining URLs using URLInterface
     */
    public function getUrlInterfaceData()
    {
        echo $this->_urlInterface->getCurrentUrl() . '<br />';
        
        echo $this->_urlInterface->getUrl() . '<br />';
        
        echo $this->_urlInterface->getUrl('helloworld/general/enabled') . '<br />';
        
        echo $this->_urlInterface->getBaseUrl() . '<br />';
    }
    
    public function getPathUrl()
    {
        $currentUrl =  $this->_urlInterface->getCurrentUrl();
        $baseUrl =  $this->_urlInterface->getUrl();
        $pathUrl = str_replace($baseUrl,"", $currentUrl);
        return $pathUrl;
    }
    
}
?>