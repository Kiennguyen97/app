<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Mpanel\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\EavValidationRules;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class DataProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{

    private $categoryHelper;
    protected $storeManager;

    /**
     * DataProvider constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param EavValidationRules $eavValidationRules
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param Config $eavConfig
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CategoryFactory $categoryFactory
     * $param \MGS\Mmegamenu\Helper\Category $categoryHelper
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EavValidationRules $eavValidationRules,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        Config $eavConfig,
        \Magento\Framework\App\RequestInterface $request,
        CategoryFactory $categoryFactory,
        \MGS\Mmegamenu\Helper\Category $categoryHelper,
        array $meta = [],
        array $data = []
    ) {

        $this->categoryHelper = $categoryHelper;

        parent::__construct($name,
            $primaryFieldName,
            $requestFieldName,
            $eavValidationRules,
            $categoryCollectionFactory,
            $storeManager,
            $registry,
            $eavConfig,
            $request,
            $categoryFactory,
            $meta,
            $data);
            $this->storeManager = $storeManager;
    }


    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $category = $this->getCurrentCategory();

        if ($category) {

            parent::getData();
            // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            // $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $categoryData = $this->loadedData[$category->getId()];
            $currentStore = $this->storeManager->getStore();
            $media_url = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            foreach ($this->categoryHelper->getAdditionalImageTypes() as $imageType) {
                if (isset($categoryData[$imageType])) {
                    
                    $name = $categoryData[$imageType][0]['name'];
                    unset($categoryData[$imageType]);

                    $categoryData[$imageType][0]['name'] = $name;
                    $categoryData[$imageType][0]['url'] = $media_url."catalog/tmp/category/".$name;
                }
            }

            $this->loadedData[$category->getId()] = $categoryData;
        }

        return $this->loadedData;

    }

}
