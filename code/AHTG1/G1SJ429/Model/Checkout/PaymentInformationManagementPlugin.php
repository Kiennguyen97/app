<?php

namespace AHTG1\G1SJ429\Model\Checkout;

use Magento\Setup\Module\Di\Compiler\Log\Writer\Console;

class PaymentInformationManagementPlugin
{

    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $extAttributes = $paymentMethod->getExtensionAttributes();
        $orderComment = $extAttributes->getOrderComment();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setOrderComment($orderComment);
        $quote->setDataChanges(true); //TODO: Phải có vì nếu ko có sẽ ko save vào quote do billaddress không có vì đã đăng nhập bill trong thư mục training có module github save vào thẳng order
    }
}
