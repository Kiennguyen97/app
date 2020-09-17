<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gssi\Custom\Block\Customer;

class Data extends \Magento\Framework\View\Element\Template
{

    protected $_customerRepositoryInterface;
    protected $_customerFactory;
    protected $customerSession;
    protected $logo;
    protected $storeRepository;
    protected $websiteCollectionFactory;
    protected $storeManagerInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->logo = $logo;
        $this->storeRepository = $storeRepository;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    public function getStoreDefaultID($website_code){
        $collection = $this->websiteCollectionFactory->create()->addFieldToFilter('code', ['in' => $website_code]);
        $websiteData = $collection->getData();
        $websiteId = $websiteData[0]['website_id'];
        $default_store_id = $this->storeManagerInterface->getWebsite($websiteId)->getDefaultGroup()->getDefaultStoreId();
        
        return $default_store_id;
    }

    public function getStoreUrl ($website_code,$store_id = null){
        if ($store_id == null) {
            $store_id = $this->getStoreDefaultID($website_code);
        }
        $store = $this->storeRepository->getById($store_id);
        return $store->getBaseUrl();
    }

    public function getCurrentStore(){
        return $this->storeManagerInterface->getStore()->getBaseUrl();
    }

    public function isCustomerLoggedIn() {
        return $this->customerSession->isLoggedIn();
    }

    public function getLogoSrc()
    {    
        return $this->logo->getLogoSrc();
    }

    public function getCustomerById() {
        return $this->_customerFactory->create()->load($this->getCustomerId());
    }
    
}