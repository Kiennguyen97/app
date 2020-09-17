<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product in category grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Bnkr\ProductOfCategory\Block\Adminhtml\Category\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

class CustomProduct  extends \Magento\VisualMerchandiser\Block\Adminhtml\Category\Merchandiser\Grid
{

    /**
     * @return Grid
     */

     /**
     * Set collection object
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        $collection->addAttributeToSelect(
            'sh_drop'
        )->addAttributeToSelect(
            'ns_drop'
        );
        parent::setCollection($collection);
    }
   
    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('sh_drop', ['header' => __('SH_Drop'), 'index' => 'sh_drop'], 'sku');
        $this->addColumnAfter('ns_drop', ['header' => __('NS_Drop'), 'index' => 'ns_drop'], 'sku');
        return $this;
    }

}
