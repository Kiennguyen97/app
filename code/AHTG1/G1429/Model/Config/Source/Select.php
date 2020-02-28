<?php
namespace AHTG1\G1429\Model\Config\Source;
class Select implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve Custom Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Category')],
            ['value' => 2, 'label' => __('Product sku')],
        ];
    }
}