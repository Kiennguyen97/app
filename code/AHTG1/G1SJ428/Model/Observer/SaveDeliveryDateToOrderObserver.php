<?php
namespace AHTG1\G1SJ428\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveDeliveryDateToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        // $quote = Mage::getSingleton('checkout/session')->getQuote(); TODO: sai
        // $quote = $observer->getEvent()->getQuote(); // TODO: dung
        $quote = $observer->getQuote(); //TODO: dung


        // $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
        // /** @var \Magento\Quote\Model\Quote $quote */
        // $quote = $quoteRepository->get($order->getQuoteId());
        $order->setDeliveryDate( $quote->getDeliveryDate() );
        $order->setCustomText( $quote->getCustomText() );
        // $order->setOrderComment( $quote->getOrderComment() );
        return $this;
    }

    //TODO: cách khác
    // public function execute(\Magento\Framework\Event\Observer $observer) {
    //     $order = $observer->getEvent()->getOrder();
    //     $quote = $observer->getEvent()->getQuote();

    //     $order->setData('customvar', $quote->getCustomvar());

    //     return $this;
    // }

}