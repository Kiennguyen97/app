<?php

namespace Gssi\OrderXml\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface {

    /**
     *
     * @var Psr\Log\LoggerInterface 
     */
    protected $logger;

    /**
     *
     * @var \Gssi\OrderXml\Helper\Data
     */
    protected $helper;

    /**
     *
     * @var \Magento\Sales\Api\OrderRepositoryInterface 
     */
    protected $orderRepository;

    /**
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface 
     */
    protected $customerRepository;

    /**
     *
     * @var \Magento\Framework\Filesystem\Driver\File 
     */
    protected $driverFile;

    /**
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList 
     */
    protected $directory_list;

    /**
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface 
     */
    protected $addressRepository;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $storeConfig;

    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     *
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $_storeManager;
    protected $_countryFactory;
    protected $cscHelper;
    protected $taxHelper;
    private $_importErrors = [];
    protected $_errorReportHelper;

    /**
     * 
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Gssi\OrderXml\Helper\Data $helper
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Catalog\Model\ProductFactory $_productFactory
     */
    public function __construct(
    \Psr\Log\LoggerInterface $logger, \Magento\Store\Model\StoreManagerInterface $storeManager, \Gssi\OrderXml\Helper\Data $helper, \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Customer\Api\AddressRepositoryInterface $addressRepository, \Magento\Framework\Filesystem\Driver\File $driverFile, \Magento\Framework\App\Filesystem\DirectoryList $directory_list, \Magento\Framework\App\Config\ScopeConfigInterface $config, \Magento\Catalog\Model\ProductFactory $_productFactory, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $optionCollection, \Magento\Directory\Model\CountryFactory $countryFactory, \Gssi\OrderXml\Helper\ConvertSpecialCharacters $cscHelper, \Magento\Tax\Helper\Data $taxHelper,
    \Gssi\ErrorReporter\Helper\EmailSender $errorReportHelper
    ) {
        $this->logger = $logger;
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
        $this->cscHelper = $cscHelper;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->driverFile = $driverFile;
        $this->directory_list = $directory_list;
        $this->storeConfig = $config;
        $this->optionCollection = $optionCollection;
        $this->_productFactory = $_productFactory;
        $this->_countryFactory = $countryFactory;
        $this->taxHelper = $taxHelper;
        $this->_errorReportHelper = $errorReportHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();

        // try {
            $topCode = '';
            $websiteCode = $this->_storeManager->getStore($order->getStoreId())->getWebsite()->getCode();

            // website code check by live site (not nexcess).
            if ($websiteCode == 'website_public') {
                $topCode = 'TMB_PUBLIC_';
            }
            if ($websiteCode == 'TimbermartBCWebSite') {
                $topCode = 'TMB_BC_';
            }
            if ($websiteCode == 'TimbermartONWebSite') {
                $topCode = 'TMB_ON_';
            }
            if ($websiteCode == 'TimbermartQCWebSite') {
                $topCode = 'TMB_QC_';
            }
            
            $fileName = $topCode . 'MAGENTO_' . $order->getIncrementId() . '_' . date('Y_m_d') . '.xml';
            $domTree = new \DOMDocument('1.0', 'UTF-8');
            $domTree->formatOutput = true;

            /* append it to the document created */
            $xmlRoot = $domTree->appendChild($domTree->createElement('Order'));
            /* format output */

            /* Order Head */
            $this->appendOrderHead($xmlRoot, $domTree, $order);
            /* Order Head */

            /* Append Customer infor */
            $this->appendCustomerInfo($xmlRoot, $domTree, $order);

            /* Append Shipping information */
            $this->appendDelivery($xmlRoot, $domTree, $order);

            /* Append Invoice To */
            $this->appendInvoiceTo($xmlRoot, $domTree, $order);

            /* Append Order Lines */
            $this->appendOrderLines($xmlRoot, $domTree, $order);

            /* Append Order Summary */
            $this->appendOrderSummary($xmlRoot, $domTree, $order);

        if (count($this->_importErrors) <= 0) {
            $xmlContent = $domTree->saveXML();
            $xmlContent = preg_replace('/^[^\n\r]*[\n\r]+/m', '', $xmlContent, 1);
            $folderPath = $this->directory_list->getRoot() . '/pub/media/orderxml';
            if (!$this->driverFile->isExists($folderPath)) {
                $this->driverFile->createDirectory($folderPath);
            }
            $this->driverFile->filePutContents($folderPath . '/' . $fileName, $xmlContent);

        // upload file to SFTP
            $dataFile = $folderPath . '/' . $fileName;
            $sftpServer = $this->storeConfig->getValue('gssi_orderxml/sftp/host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $sftpUsername = $this->storeConfig->getValue('gssi_orderxml/sftp/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $sftpPassword = $this->storeConfig->getValue('gssi_orderxml/sftp/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $sftpPort = $this->storeConfig->getValue('gssi_orderxml/sftp/port', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $sftpRemoteDir = $this->storeConfig->getValue('gssi_orderxml/sftp/path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $ch = curl_init('sftp://' . $sftpServer . ':' . $sftpPort . $sftpRemoteDir . '/' . basename($dataFile));
            $fh = fopen($dataFile, 'r');
            if ($fh) {
                curl_setopt($ch, CURLOPT_USERPWD, $sftpUsername . ':' . $sftpPassword);
                curl_setopt($ch, CURLOPT_UPLOAD, true);
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
                curl_setopt($ch, CURLOPT_INFILE, $fh);
                curl_setopt($ch, CURLOPT_INFILESIZE, filesize($dataFile));
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $verbose = fopen('php://temp', 'w+');
                curl_setopt($ch, CURLOPT_STDERR, $verbose);
                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);
                if ($response) {
                } else {
                    rewind($verbose);
                    $verboseLog = stream_get_contents($verbose);
                    $this->logger->critical($verboseLog);
                }
            }
        }
        // } catch (\Exception $exc) {
        //     $this->logger->critical('orderxml_sales_order_save_after');
        //     $this->logger->critical($exc->getTraceAsString());
            /* SEND EMAIL REPORT */

			if (count($this->_importErrors)) {
                $emailContent = 'Order ID: '.$this->cscHelper->ConvertSpecialCharacters($order->getIncrementId()).'_' . date('Y_m_d') . ' at '.$this->_storeManager->getStore($order->getStoreId())->getWebsite()->getCode().' generate failed by errors: '."\n"; 
                
				foreach ($this->_importErrors as $errorMessage) {
					$emailContent .= $errorMessage;
					$emailContent .= "\n\t";
				}
				$this->_errorReportHelper->sendOrderXmlErrorReportEmail(['errorMessage' => $emailContent]);
			}
        // }
    }

    /**
     * 
     * @param type $xmlRoot
     * @param type $domTree
     * @param type $order
     * @return void
     */
    protected function appendOrderHead($xmlRoot, $domTree, $order) {
        try {
            $cscHelper = $this->cscHelper;
            $orderHead = $xmlRoot->appendChild($domTree->createElement('OrderHead'));
            //$TestFlag = $orderHead->appendChild($domTree->createElement('TestFlag'));
            //$Test = $TestFlag->appendChild($domTree->createElement('Test', ''));
            //$Test->setAttribute('Mode', '');
            $orderHead->appendChild($domTree->createElement('MagentoOrderNumber', $cscHelper->ConvertSpecialCharacters($order->getIncrementId())));
            $websiteCode = $this->_storeManager->getStore($order->getStoreId())->getWebsite()->getCode();
            $orderHead->appendChild($domTree->createElement('MagentoWebsiteID', $websiteCode));
            $orderHead->appendChild($domTree->createElement('OrderType', 'Order'));
            $orderHead->appendChild($domTree->createElement('Currency', $cscHelper->ConvertSpecialCharacters($order->getOrderCurrencyCode())));
            $orderHead->appendChild($domTree->createElement('OrderDate', date('Y-m-d', strtotime($order->getCreatedAtFormatted(\IntlDateFormatter::SHORT)))));
        } catch (\Exception $exc) {
            $this->_importErrors[] = 'OrderHeader: '.$exc->getMessage(); 
        }
        
    }

    /**
     * 
     * @param type $xmlRoot
     * @param type $domTree
     * @param type $order
     * @return void
     */
    protected function appendCustomerInfo($xmlRoot, $domTree, $order) {
        try {
            $cscHelper = $this->cscHelper;
            $customerInfo = $domTree->createElement('CustomerInfo');
            $customerInfo = $xmlRoot->appendChild($customerInfo);
            $customerId = $order->getCustomerId();
            if ($customerId) {
                $customer = $this->helper->getModel('Magento\Customer\Model\Customer')->load($customerId);
                if ($customer->getId()) {
                    //                if ($customer->getTbmErpcode()) {
                    //                    $customerCode = $customer->getTbmErpcode();
                    //                } else {
                    //                    $customerCode = '';
                    //                }
                    $customerAttributes = $this->helper->getModel('Magento\Customer\Api\CustomerRepositoryInterface')->getById($customer->getId())->getExtensionAttributes()->getCompanyAttributes();
                    $companyId = $customerAttributes->getCompanyId();
                    if ($companyId) {
                        try {
                            $company = $this->helper->getModel('Magento\Company\Api\CompanyRepositoryInterface')->get($companyId);
                            $companyName = '';
                            if ($company) {
                                $companyName = $company->getCompanyName();
                                $customerCode = $company->getCompanyErpId();
                            }
                        } catch (\Exception $e) {
                            $companyName = '';
                            $customerCode = '';
                        }
                    } else {
                        $companyName = '';
                        $customerCode = '';
                    }
                } else {
                    $customerCode = '';
                    $companyName = '';
                }
            } else {
                $customerCode = '';
                $companyName = '';
            }
            //        if ($companyName == '') {
            //            $shippingAddressId = $order->getShippingAddressId();
            //            $shippingAddress = $this->helper->getModel('Magento\Sales\Model\Order\Address')->load($shippingAddressId);
            //            $companyName = $shippingAddress->getCompany();
            //        }
            $customerInfo->appendChild($domTree->createElement('ERPCustomerCode', $customerCode));
            $customerInfo->appendChild($domTree->createElement('FirstName', $cscHelper->ConvertSpecialCharacters($order->getCustomerFirstname())));
            $customerInfo->appendChild($domTree->createElement('LastName', $cscHelper->ConvertSpecialCharacters($order->getCustomerLastname())));
            $customerInfo->appendChild($domTree->createElement('Email', $cscHelper->ConvertSpecialCharacters($order->getCustomerEmail())));
            $customerInfo->appendChild($domTree->createElement('CompanyName', $cscHelper->ConvertSpecialCharacters($companyName)));
        } catch (\Exception $exc) {
            $this->_importErrors[] = 'Customer Info:'.$exc->getMessage();
        }
        
    }

    /**
     * 
     * @param type $xmlRoot
     * @param type $domTree
     * @param type $order
     * @return void
     */
    protected function appendDelivery($xmlRoot, $domTree, $order) {
        try {
            $cscHelper = $this->cscHelper;
            $shipping = $domTree->createElement('Delivery');
            $shipping = $xmlRoot->appendChild($shipping);
            $shippingAddressId = $order->getShippingAddressId();
            $shippingAddress = $this->helper->getModel('Magento\Sales\Model\Order\Address')->load($shippingAddressId);
            if ($shippingAddress->getCustomerAddressId()) {
                $customerAddress = $this->helper->getModel('Magento\Customer\Model\Address')->load($shippingAddress->getCustomerAddressId());
                if ($customerAddress->getTbmErpcode()) {
                    $ERPAddressCode = $customerAddress->getTbmErpcode();
                } else {
                    $ERPAddressCode = '';
                }
            } else {
                $ERPAddressCode = '';
            }
            $street = $shippingAddress->getStreet();
            $countryName = $this->helper->getModel('Magento\Directory\Model\Country')->load($order->getShippingAddress()->getCountryId())->getName();
            $deliveryTo = $shipping->appendChild($domTree->createElement('DeliverTo'));
            $deliveryTo->appendChild($domTree->createElement('ERPAddressCode', $ERPAddressCode));
            if (isset($street[0])) {
                $deliveryTo->appendChild($domTree->createElement('AddressLine1', $cscHelper->ConvertSpecialCharacters($street[0])));
            } else {
                $deliveryTo->appendChild($domTree->createElement('AddressLine1', ''));
            }
            if (isset($street[1])) {
                $deliveryTo->appendChild($domTree->createElement('AddressLine1', $cscHelper->ConvertSpecialCharacters($street[1])));
            } else {
                $deliveryTo->appendChild($domTree->createElement('AddressLine2', ''));
            }
            if (isset($street[2])) {
                $deliveryTo->appendChild($domTree->createElement('AddressLine1', $cscHelper->ConvertSpecialCharacters($street[2])));
            } else {
                $deliveryTo->appendChild($domTree->createElement('AddressLine3', ''));
            }
            $deliveryTo->appendChild($domTree->createElement('Country', $cscHelper->ConvertSpecialCharacters($countryName)));
            $deliveryTo->appendChild($domTree->createElement('City', $cscHelper->ConvertSpecialCharacters($order->getShippingAddress()->getCity())));
            $deliveryTo->appendChild($domTree->createElement('State', $cscHelper->ConvertSpecialCharacters($order->getShippingAddress()->getRegion())));
            $deliveryTo->appendChild($domTree->createElement('Zipcode', $cscHelper->ConvertSpecialCharacters($order->getShippingAddress()->getPostcode())));
            $deliveryTo->appendChild($domTree->createElement('PhoneNumber', $cscHelper->ConvertSpecialCharacters($order->getShippingAddress()->getTelephone())));
            $DeliverMethod = $shipping->appendChild($domTree->createElement('DeliverMethod'));
            $DeliverMethod->appendChild($domTree->createElement('ShippingProvider', $cscHelper->ConvertSpecialCharacters($order->getShippingDescription())));
            $DeliverMethod->appendChild($domTree->createElement('ValueQuoted', ''));
            $payment = $order->getPayment();
            $method = $payment->getMethodInstance();
            $methodTitle = $method->getTitle();
            $methodCode = $payment->getMethod();
            if ($methodCode == 'paymentquoterequest') {
                $shipping->appendChild($domTree->createElement('PaymentMethod', $cscHelper->ConvertSpecialCharacters('Quote Request')));
            } else {
                $shipping->appendChild($domTree->createElement('PaymentMethod', $cscHelper->ConvertSpecialCharacters($methodTitle)));
            }
            $shipping->appendChild($domTree->createElement('BuyersPONumber', $cscHelper->ConvertSpecialCharacters($order->getPayment()->getPoNumber())));
            $orderComment = [];
            foreach ($order->getStatusHistoryCollection() as $status) {
                if ($status->getComment()) {
                    $orderComment[] = $status->getComment();
                }
            }
            $comments = implode(' | ', $orderComment);
            $shipping->appendChild($domTree->createElement('OrderNotes', $cscHelper->ConvertSpecialCharacters('')));
            $shipping->appendChild($domTree->createElement('OrderComments', $cscHelper->ConvertSpecialCharacters($comments)));
        } catch (\Exception $exc) {
            $this->_importErrors[] = 'Delivery: '.$exc->getMessage();
        }

        
    }

    /**
     * 
     * @param type $xmlRoot
     * @param type $domTree
     * @param type $order
     * @return void
     */
    protected function appendInvoiceTo($xmlRoot, $domTree, $order) {
        try {
            $cscHelper = $this->cscHelper;
            $InvoiceTo = $domTree->createElement('InvoiceTo');
            $InvoiceTo = $xmlRoot->appendChild($InvoiceTo);
            $billingAddressId = $order->getBillingAddressId();
            $billingAddress = $this->helper->getModel('Magento\Sales\Model\Order\Address')->load($billingAddressId);
            if ($billingAddress->getCustomerAddressId()) {
                $customerAddress = $this->helper->getModel('Magento\Customer\Model\Address')->load($billingAddress->getCustomerAddressId());
                if ($customerAddress->getTbmErpcode()) {
                    $ERPAddressCode = $customerAddress->getTbmErpcode();
                } else {
                    $ERPAddressCode = '';
                }
            } else {
                $ERPAddressCode = '';
            }
            $street = $billingAddress->getStreet();
            $countryName = $this->helper->getModel('Magento\Directory\Model\Country')->load($order->getBillingAddress()->getCountryId())->getName();
            $InvoiceTo->appendChild($domTree->createElement('ERPAddressCode', $ERPAddressCode));
            if (isset($street[0])) {
                $InvoiceTo->appendChild($domTree->createElement('AddressLine1', $cscHelper->ConvertSpecialCharacters($street[0])));
            } else {
                $InvoiceTo->appendChild($domTree->createElement('AddressLine1', ''));
            }
            if (isset($street[1])) {
                $InvoiceTo->appendChild($domTree->createElement('AddressLine2', $cscHelper->ConvertSpecialCharacters($street[1])));
            } else {
                $InvoiceTo->appendChild($domTree->createElement('AddressLine2', ''));
            }
            if (isset($street[2])) {
                $InvoiceTo->appendChild($domTree->createElement('AddressLine3', $cscHelper->ConvertSpecialCharacters($street[2])));
            } else {
                $InvoiceTo->appendChild($domTree->createElement('AddressLine3', ''));
            }
            $InvoiceTo->appendChild($domTree->createElement('Country', $cscHelper->ConvertSpecialCharacters($countryName)));
            $InvoiceTo->appendChild($domTree->createElement('City', $cscHelper->ConvertSpecialCharacters($order->getBillingAddress()->getCity())));
            $InvoiceTo->appendChild($domTree->createElement('State', $cscHelper->ConvertSpecialCharacters($order->getBillingAddress()->getRegion())));
            $InvoiceTo->appendChild($domTree->createElement('Zipcode', $cscHelper->ConvertSpecialCharacters($order->getBillingAddress()->getPostcode())));
            $InvoiceTo->appendChild($domTree->createElement('PhoneNumber', $cscHelper->ConvertSpecialCharacters($order->getBillingAddress()->getTelephone())));
        } catch (\Exception $exc) {
            $this->_importErrors[] = 'Invoice To: '.$exc->getMessage();
        }
        
    }

    /**
     * 
     * @param type $xmlRoot
     * @param type $domTree
     * @param type $order
     * @return void
     */
    protected function appendOrderLines($xmlRoot, $domTree, $order) {
        try {
            $orderItems = $order->getAllItems();
            $cscHelper = $this->cscHelper;
            $OrderLines = $domTree->createElement('OrderLines');
            $OrderLines = $xmlRoot->appendChild($OrderLines);
            $count = 1;
            $priceHelper = $this->helper->getModel('Magento\Framework\Pricing\Helper\Data');
            foreach ($orderItems as $item) {
                $productItem = $item->getProduct();
                $OrderLine = $domTree->createElement('OrderLine');
                $OrderLine = $OrderLines->appendChild($OrderLine);
                $OrderLine->appendChild($domTree->createElement('SKU', $cscHelper->ConvertSpecialCharacters($productItem->getSku())));
                $OrderLine->appendChild($domTree->createElement('ProductName', $cscHelper->ConvertSpecialCharacters($productItem->getName())));
                //$LineNumber = $OrderLine->appendChild($domTree->createElement('LineNumber', $count++));
                //$Product = $OrderLine->appendChild($domTree->createElement('Product'));
                //$SKU = $Product->appendChild($domTree->createElement('SKU', $cscHelper->ConvertSpecialCharacters($productItem->getSku())));
                //$Description = $Product->appendChild($domTree->createElement('Description', $cscHelper->ConvertSpecialCharacters($productItem->getName())));
                $Quantity = $OrderLine->appendChild($domTree->createElement('Quantity'));
                $qtyOrdered = $item->getQtyOrdered();
                $Amount = $Quantity->appendChild($domTree->createElement('Amount', $qtyOrdered));
                $UnitOfMeasureValue = "";
                $timbermartUom = $productItem->getResource()->getAttribute('tbm_product_uom');
                if ($timbermartUom) {
                    $_attributeValue = $productItem->getAttributeText('tbm_product_uom');
                    $optionId = $timbermartUom->getSource()->getOptionId($_attributeValue);
                    $options = $this->optionCollection->create()->setAttributeFilter($timbermartUom->getId())->setStoreFilter(0)->toOptionArray();
                    $adminLabel = '';
                    if (count($options)) {
                        foreach ($options as $_option) {
                            if (isset($_option['value']) && $_option['value'] == $optionId) {
                                $adminLabel = $_option['label'];
                            }
                        }
                    }
                    $UnitOfMeasureValue = $adminLabel;
                }
                if (strtolower($UnitOfMeasureValue) == "no") {
                    $UnitOfMeasureValue = "";
                }
                $UnitOfMeasure = $Quantity->appendChild($domTree->createElement('UnitOfMeasure', $UnitOfMeasureValue));
                $ProductPrice = $item->getPrice();
                $UnitPrice = $Quantity->appendChild($domTree->createElement('UnitPrice', $ProductPrice));
                $Taxes = $OrderLine->appendChild($domTree->createElement('Taxes'));
                $TaxLine = $Taxes->appendChild($domTree->createElement('TaxLine'));
                $taxClassId = $productItem->getTaxClassId();
                if ($taxClassId) {
                    $taxClass = $this->helper->getModel('Magento\Tax\Api\TaxClassRepositoryInterface')->get($taxClassId);
                    $TaxLine->appendChild($domTree->createElement('Label', $cscHelper->ConvertSpecialCharacters($taxClass->getData('class_name'))));
                    $TaxLine->appendChild($domTree->createElement('Rate', $cscHelper->ConvertSpecialCharacters(number_format((float) $item->getTaxPercent(), 2, '.', '') . '%')));
                    $TaxLine->appendChild($domTree->createElement('Total', $cscHelper->ConvertSpecialCharacters($priceHelper->currency($item->getTaxAmount(), true, false))));
                } else {
                    $TaxLine->appendChild($domTree->createElement('Label', 'None Tax'));
                    $TaxLine->appendChild($domTree->createElement('Rate', '0.00%'));
                    $TaxLine->appendChild($domTree->createElement('Total', '$0.00'));
                }

                $subtotal = $ProductPrice * $qtyOrdered;
                $LineSubtotal = $OrderLine->appendChild($domTree->createElement('LineSubtotal', $subtotal));
            }
        } catch (\Exception $exc) {
            $this->_importErrors[] = 'Order Lines: '.$exc->getMessage();
        }
        
    }

    /**
     * 
     * @param type $xmlRoot
     * @param type $domTree
     * @param type $order
     * @return void
     */
    protected function appendOrderSummary($xmlRoot, $domTree, $order) {
        try {
            $cscHelper = $this->cscHelper;
            $orderSummary = $domTree->createElement('OrderSummary');
            $orderSummary = $xmlRoot->appendChild($orderSummary);
            $ShippingTotal = $orderSummary->appendChild($domTree->createElement('ShippingTotal', $order->getShippingAmount()));
            $TaxTotal = $orderSummary->appendChild($domTree->createElement('TaxTotal', $order->getTaxAmount()));
            $ItemsSubtotal = $orderSummary->appendChild($domTree->createElement('ItemsSubtotal', $order->getSubtotal()));
            $DiscountTotal = $orderSummary->appendChild($domTree->createElement('DiscountTotal', $order->getDiscountAmount()));
            $OrderTotal = $orderSummary->appendChild($domTree->createElement('OrderTotal', $order->getGrandTotal()));
        } catch (\Exception $exc) {
            $this->_importErrors[] = 'Order Summary: '. $exc->getMessage();
        }
        
    }

}
