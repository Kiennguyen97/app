<?php
/**
 * Source for email send method
 *
 */
namespace Gssi\ErrorReporter\Model\Config\Source\Email;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 'cc', 'label' => __('Cc')],
            ['value' => 'bcc', 'label' => __('Bcc')],
        ];
        return $options;
    }
}
