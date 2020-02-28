<?php

namespace AHTG1\G1429\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Category implements ArrayInterface
{
    protected $_categoryHelper;
    protected $categoryRepository;
    protected $categoryList;
    protected $_categoryCollectionFactory;
    protected $i = 0;
    protected $k = 0;
    protected $arr;
    protected $arr2;
    protected $arr3;


    public function __construct(
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->_categoryHelper = $catalogCategory;
        $this->categoryRepository = $categoryRepository;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    /*
     * Return categories helper
     */

    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }

    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }
    /*  
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
        $this->toArray();
        $categoryList = $this->categoryList;
        foreach ($categoryList as $k => $v) {
            $child = [];
            foreach ($v as $k1 => $v1) {
                $child[] = [
                    'label' => $v1,
                    'value' => $k1
                ];
            }
            $ret[] = [
                'label' => $this->arr[$k],
                'value' => $child
            ];
        }
        return $ret;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */




    public function toArray()
    {

        $categories = $this->getCategoryCollection(true, false, false, false);
        $i = 1;
        foreach ($categories as $category) {
            $categoryObj = $this->categoryRepository->get($category->getId());
            if ($categoryObj->getLevel() == 1) {
                $this->categoryList[1][$categoryObj->getId()] = $categoryObj->getName();
                $this->arr[1] = 'ROOT';
                // $this->arr2[1] = 'ROOT';
            }
        }
        foreach ($categories as $category) {
            $categoryObj = $this->categoryRepository->get($category->getId());
            $parentName = $this->_getParentName($categoryObj->getPath());
            if ($categoryObj->hasChildren()) {
                $i++;
                if ($parentName!='') {
                    $this->arr[$i] = $parentName . '->' . $categoryObj->getName();
                }else {
                    $this->arr[$i] = $categoryObj->getName();
                }
                // $this->arr2[$i] = $categoryObj->getName();
                $subcategories = $categoryObj->getChildrenCategories();
                foreach ($subcategories as $subcategory) {
                    $this->categoryList[$i][$subcategory->getEntityId()] = __($subcategory->getName());
                }
            }
            // echo "<script>console.log('" . $parentName . "' );</script>";

        }
        //TODO: show collection

        // $this->i = $i;

        // echo "<script>console.log('" . $this->i . "' );</script>";
        // $k = 1;
        // $a = $i;

        // foreach ($categories as $category) {
        //     $categoryObj = $this->categoryRepository->get($category->getId());
        //     $parentName = $this->_getParentName($categoryObj->getPath());
        //     if ($this->checkParent2($parentName)) {
        //         $this->categoryList[$a + $this->checkParent2($parentName)][$categoryObj->getId()] = $categoryObj->getName();
        //     } else {
        //         if (!$categoryObj->hasChildren() && !$this->checkParent1($parentName)) {
        //             $i++;
        //             $this->i = $i;
        //             $this->arr[$i] = $parentName;
        //             $this->arr2[$i] = $parentName;
        //             $this->categoryList[$i][$categoryObj->getId()] = $categoryObj->getName();
        //             $this->arr3[$k] = $parentName;
        //             $this->k = $k;
        //             $k++;
        //             // echo "<script>console.log('" . $parentName . "' );</script>";

        //         }
        //     }
        //     echo "<script>console.log('" . $parentName . "' );</script>";
        // }
        // echo "<script>console.log('" . $this->i . "' );</script>";
    }

    //TODO: show collection
    // public function checkParent1($parentName)
    // {
    //     for ($j = 1; $j <= $this->i; $j++) {
    //         if ($parentName == $this->arr2[$j]) {
    //             return true;
    //         }
    //     }
    //     return false;
    // }
    // public function checkParent2($parentName)
    // {
    //     for ($j = 1; $j <= $this->k; $j++) {
    //         if ($parentName == $this->arr3[$j]) {
    //             return $j;
    //         }
    //     }
    //     return false;
    // }



    private function _getParentName($path = '')
    {
        $parentName = '';
        $rootCats = array(1, 2);

        $catTree = explode("/", $path);
        // Deleting category itself
        array_pop($catTree);

        if ($catTree && (count($catTree) > count($rootCats))) {
            foreach ($catTree as $catId) {
                if (!in_array($catId, $rootCats)) {
                    $category = $this->categoryRepository->get($catId);
                    $categoryName = $category->getName();
                    $parentName .= $categoryName;
                }
            }
        }

        return $parentName;
    }
}
