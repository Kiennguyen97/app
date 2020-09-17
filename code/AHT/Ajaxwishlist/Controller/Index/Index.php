<?php

namespace Bnkr\Ajaxwishlist\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Index
 *
 * @package Bnkr\Ajaxwishlist\Controller\Index
 */
class Index extends Action
{

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var WishlistFactory
     */
    protected $wishlistRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param WishlistFactory $wishlistRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ResultFactory $resultFactory
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        WishlistFactory $wishlistRepository,
        ProductRepositoryInterface $productRepository,
        ResultFactory $resultFactory,
        JsonFactory $jsonFactory
    ) {
        $this->customerSession = $customerSession;
        $this->wishlistRepository= $wishlistRepository;
        $this->productRepository = $productRepository;
        $this->resultFactory = $resultFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $productId = $this->getRequest()->getParam('productId');
        $array = [];
        array_push($array, $productId);
        if(count($array) == 1){
            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }
            if ($product) {
                $wishlist = $this->wishlistRepository->create()->loadByCustomerId($customerId, true);
                $wishlist->addNewItem($product);
                $wishlist->save();
                // $this->messageManager->addSuccess(__("Item added to wishlist"));
            }
        }

        $result = $this->jsonFactory->create();
        return $result;
    }
}
