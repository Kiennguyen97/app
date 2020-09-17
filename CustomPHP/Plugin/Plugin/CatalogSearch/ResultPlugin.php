<?php

namespace Convert\Catalog\Plugin\CatalogSearch;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\CatalogSearch\Block\Result as Subject;

class ResultPlugin extends Template{

    protected $catalogSearchData;

    public function __construct(
        Context $context,
        Data $catalogSearchData,
        array $data = []
    ) {
        $this->catalogSearchData = $catalogSearchData;
        parent::__construct($context, $data);
    }

    public function afterGetSearchQueryText(Subject $subject, $result)
    {   
        if ($this->catalogSearchData->getEscapedQueryText() == '') {
            return __("All Products");
        }else {
            return $result;
        }
    }
}