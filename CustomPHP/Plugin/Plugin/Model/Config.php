<?php

namespace Convert\Catalog\Plugin\Model;

use Magento\Catalog\Model\Config as Subject;

/**
 * Class Config
 *
 * @package Convert\Catalog\Plugin\Model
 */
class Config
{
    /**
     * @param Subject $catalogConfig
     * @param $options
     * @return mixed
     */
    public function afterGetAttributeUsedForSortByArray(Subject $catalogConfig, $options)
    {
        unset($options['position']);
        unset($options['name']);
        unset($options['price']); 
        unset($options['size']);
        $options['sort_by'] = __('Sort by');
        $options['sort_position'] = __('Position');
        $options['name'] = __('Product Name');
        $options['price'] = __('Price');
        return $options;
    }
}