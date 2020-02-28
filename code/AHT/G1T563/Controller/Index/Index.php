<?php

namespace AHT\G1T563\Controller\Index;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Url;

class Index extends \Magento\Framework\App\Action\Action
{
    const DEFAULT_TITLE = 'Kien-Helloworld-Page';
    protected $_scopeConfig;
    protected $resultPageFactory;
    protected $resultForwardFactory;
    protected $_http;
    protected $_url;
    protected $_urlRewriteFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Http $http,
        Url $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory
    ) {
        $this->_http = $http;
        $this->resultPageFactory = $resultPageFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_url = $url;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        parent::__construct($context);
    }

    public function execute()
    {
//TODO: False
        // $url_key = $this->_scopeConfig->getValue('helloworld/general/url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // if (!$url_key) {
        //     // $urlRewriteCollection->setRequestPath($url_key);
        //     // $urlRewriteCollection->save();
        // }

        // $this->_http->setRouteName('aht');
        // $this->_http->setControllerName('');
        // $this->_http->setActionName('');
        // $this->_http->setRoutePath('aht');
        // $this->_url->setRoutePath('aht');
//TODO: get url collection
        // $urlRewriteModel = $this->_urlRewriteFactory->create();
        // $urlRewriteModel->setTargetPath("helloworld/index/index");
        // $urlRewriteModel->setRequestPath("aht");
        // $urlRewriteModel->save();



        $pageTitle = $this->_scopeConfig->getValue('helloworld/general/pagetitle', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $resultPage = $this->_scopeConfig->getValue('helloworld/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$resultPage) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('defaultNoRoute');
            // $resultRedirect = $this->resultRedirectFactory->create();
            // $resultRedirect->setPath('helloworld/index/defaultnoroute');
            return $resultForward;
            // return $resultRedirect;
        } else {
            /*Module is enabled */
            $page =  $this->resultPageFactory->create();
            if (!$pageTitle) {
                $page->getConfig()->getTitle()->set(self::DEFAULT_TITLE);
            } else {
                $page->getConfig()->getTitle()->set($pageTitle);
            }
            return $page;
        }
    }
}
