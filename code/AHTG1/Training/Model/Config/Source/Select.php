<?php
namespace AHTG1\Training\Model\Config\Source;
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
            ['value' => 2, 'label' => __('Option 2')],
            ['value' => 3, 'label' => __('Option 3')]
        ];
    }
}