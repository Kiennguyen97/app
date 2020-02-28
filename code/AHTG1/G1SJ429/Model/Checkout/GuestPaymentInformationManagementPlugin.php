<?php

namespace AHTG1\G1SJ429\Model\Checkout;

class GuestPaymentInformationManagementPlugin
{
    protected $quoteIdMaskFactory;
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $extAttributes = $paymentMethod->getExtensionAttributes();
        $orderComment = $extAttributes->getOrderComment();
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quote = $this->quoteRepository->getActive($quoteIdMask->getQuoteId());
        $quote->setOrderComment($orderComment);
        // $quote->setDataChanges(true); không cần vì đã có billaddress
    }
}
