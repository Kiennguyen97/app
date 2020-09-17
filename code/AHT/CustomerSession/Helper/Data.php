<?php

namespace Gssi\CustomerSession\Helper;

use Magento\Store\Model\ScopeInterface;
use Gssi\CustomerSession\Model\Customer\Context as CustomerSessionContext;
use Magento\Customer\Model\Context;
use Magento\Newsletter\Model\SubscriberFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_LOG = 'purolator/';

    /**
     * @var Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        SubscriberFactory $subscriberFactory,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->_objectManager = $objectManager;
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($context);
    }
    
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function getCustomerID()
    {
        return $this->httpContext->getValue(CustomerSessionContext::CONTEXT_CUSTOMER_ID);
    }

    public function checkSubscriber(){
        $customerId = $this->httpContext->getValue(CustomerSessionContext::CONTEXT_CUSTOMER_ID);
        $subscriber = $this->_objectManager->get('Magento/Newsletter/Model/Subscriber')->loadByCustomerId($customerId);
        return $subscriber->isSubscribed();
    }
    
    public function isCustomerSubscribeById() {
        $customerId = $this->httpContext->getValue(CustomerSessionContext::CONTEXT_CUSTOMER_ID);
        $status = $this->subscriberFactory->create()->loadByCustomerId((int)$customerId)->isSubscribed();
        return (bool)$status;
    }
}