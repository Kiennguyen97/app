<?php
namespace AHTG1\G1429\Model\Config\Source;
class GroupSelect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve Custom Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionGroup = [
            [
                'label' => 'Option group1 lable',
                'value' => [
                    [
                        'label' => 'group 1 option 1',
                        'value' => 'group1-value1'
                    ],
                    [
                        'label' => 'group 1 option2',
                        'value' => 'group 1 value2'
                    ],
                ],
            ],
            [
                'label' => 'Option group2 lable',
                'value' => [
                    [
                        'label' => 'group 2 option 1',
                        'value' => 'group2-value1'
                    ],
                    [
                        'label' => 'group 2 option2',
                        'value' => 'group2-value2'
                    ],
                ],
            ]
        ];
        return $optionGroup;
    }
}