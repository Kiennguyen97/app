<?php

namespace Gssi\GoImage\Block;

class Metadata extends \Magento\Framework\View\Element\Template
{

    protected $_registry;

    protected $current_category;

    protected $current_product;

    protected $request;

    protected $logo;

    protected $pageRepository;

    protected $page;

    protected $storeManagerInterface;

    protected $configdata;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Cms\Model\PageRepository $pageRepository,
        \Magento\Cms\Model\Page $page,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Gssi\GoImage\Helper\Data $configdata,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->request = $request;
        $this->logo = $logo;
        $this->pageRepository = $pageRepository;
        $this->page = $page;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->configdata = $configdata;
    }

    public function getCurrentCategory()
    {
        $this->current_category = $this->_registry->registry('current_category');
        return $this->current_category;
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function checkPage()
    {
        return $this->request->getFullActionName();
    }

    public function getGo_imageUrl($imagename)
    {
        if ($imagename) {
            $currentStore = $this->storeManagerInterface->getStore();
            $media_url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $imageurl = $media_url . "catalog/tmp/category/" . $imagename;
            return $imageurl;
        }
        return $imagename;
    }

    public function getGo_imageDefaultUrl($imagename)
    {
        if ($imagename) {
            $currentStore = $this->storeManagerInterface->getStore();
            $media_url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $imageurl = $media_url . "cms/" . $imagename;
            return $imageurl;
        }
        return $imagename;
    }

    public function getLogoSrc()
    {
        return $this->logo->getLogoSrc();
    }

    public function getGoImageUrl()
    {
        $current_page = $this->checkPage();
        if ($current_page == 'catalog_category_view') {
            if($this->getCurrentCategory()->getImageUrl()){
                return $this->getCurrentCategory()->getImageUrl();
            }else{
                return $this->getGo_imageDefaultUrl($this->configdata->getGoImageDefault());
            }
        } elseif ($current_page == 'cms_index_index') {
            $pageIdentifier = $this->page->getIdentifier();
            $imagename = $this->pageRepository->getById($pageIdentifier)->getGo_image();
            if($this->getGo_imageUrl($imagename)){
                $imageurl = $this->getGo_imageUrl($imagename);
            }else{
                $imageurl = $this->getGo_imageDefaultUrl($this->configdata->getGoImageDefault());
            }
            return $imageurl;
        } elseif ($current_page == 'cms_page_view') {
            $pageIdentifier = $this->page->getIdentifier();
            $imagename = $this->pageRepository->getById($pageIdentifier)->getGo_image();
            if($this->getGo_imageUrl($imagename)){
                $imageurl = $this->getGo_imageUrl($imagename);
            }else{
                $imageurl = $this->getGo_imageDefaultUrl($this->configdata->getGoImageDefault());
            }
            return $imageurl;
        } else {
            return $this->getGo_imageDefaultUrl($this->configdata->getGoImageDefault());
        }
    }
}
