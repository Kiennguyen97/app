<?php
namespace app;

/**
 * @api
 * @since 100.0.2
 */
class Product 
{
    private $productRepository; 

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    // get DATA


    public function getDataProduct ($_product){
        
        $_product->getData('product_page_type');
    }

    //get Product by Sku
    public function loadMyProduct($sku)
    {
        return $this->productRepository->get($sku);
    }

    // Price Info
    public function PriceInfo ($sku){

        $product =$this->productRepository->get($sku);
        //include Tax
        $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

        //Exl Tax
        $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getBaseAmount();
        $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
        

        //Special
        $product->getPriceInfo()->getPrice('special_price')->getValue();
    }

}