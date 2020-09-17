<?php

namespace Convert\Catalog\Plugin\Product\ProductList;

use Magento\Framework\Data\Collection;
use Magento\Catalog\Block\Product\ProductList\Toolbar as Subject;

/**
 * Class Toolbar
 *
 * @package Convert\Catalog\Plugin\Product\ProductList
 */
class Toolbar
{
    /**
     * Products collection
     *
     * @var Collection
     */
    protected $_collection = null;

    /**
     * @param Subject $toolbar
     * @param \Closure $proceed
     * @param Collection $collection
     * @return mixed
     */
    public function aroundSetCollection(Subject $toolbar, \Closure $proceed, $collection)
    {
        $this->_collection = $collection;
        $currentOrder = $toolbar->getCurrentOrder();
        $currentDirection = $toolbar->getCurrentDirection();
        $result = $proceed($collection);
        if ($currentOrder) {
            switch ($currentOrder) {

                case 'sort_by':
                case 'sort_position':
                    $this->_collection
                        ->getSelect()
                        ->order('position ASC');
                    break;
                case 'name':
                    $this->_collection
                        ->getSelect()
                        ->order('name ASC');
                    break;
                case 'price':
                    $this->_collection
                        ->getSelect()
                        ->order('final_price ASC');
                    break;
                default:
                    $this->_collection
                        ->setOrder($currentOrder, $currentDirection);
                    break;
            }
        }
        return $result;
    }
}