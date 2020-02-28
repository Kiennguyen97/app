<?php

namespace AHTG1\Training\Plugin\Contact;

class Submit {
    protected $data;

    public function __construct(
        \AHTG1\Training\Helper\Data $data
    ) {
        $this->data = $data;
    }
    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject , $result)    {
        if ($this->data->getGeneralConfig('yes')==1) {
            $result->setPath('cms/index/index'); // Change this to what you want
        }
        return $result;
   }
}