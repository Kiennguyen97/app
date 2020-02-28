<?php
namespace AHT\G1T563\Model\Config\Source;
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
            ['value' => 1, 'label' => __('Option 1')],
            ['value' => 2, 'label' => __('Option 2')]
        ];
    }
}