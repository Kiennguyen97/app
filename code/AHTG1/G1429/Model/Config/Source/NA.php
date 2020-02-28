<?php
namespace AHTG1\G1429\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

class NA implements ArrayInterface
{
    protected $categoryHelper;
    protected $categoryRepository;
    protected $categoryList;
    protected $_categoryCollectionFactory;
    
    public function __construct(
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
        ){
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function setGroup($category){
        $categoryObj = $this->categoryRepository->get($category->getId()); 
        $parentName = $this->_getParentName($categoryObj->getPath());
        if($parentName!='')       
            $this->categoryList[$categoryObj->getLevel()][$categoryObj->getId()] = $parentName.$categoryObj->getName();
        else{
            $this->categoryList[$categoryObj->getLevel()][$categoryObj->getId()] = $categoryObj->getname();
        }
        if($categoryObj->hasChildren()){
            
            $subCategories = $categoryObj->getChildrenCategories();
            foreach($subCategories as $subCategory){
                $this->setGroup($subCategory);
            }
        }
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

    private function _getParentName($path = '')
    {
        $parentName = '';
        $rootCats = array(1,2);

        $catTree = explode("/", $path);
        // Deleting category itself
        array_pop($catTree);

        if($catTree && (count($catTree) > count($rootCats)))
        {
            foreach ($catTree as $catId)
            {
                if(!in_array($catId, $rootCats))
                {
                    $category = $this->categoryRepository->get($catId);
                    $categoryName = $category->getName();
                    $parentName .= $categoryName . ' -> ';
                }
            }
        }

        return $parentName;
    }
    public function toOptionArray(){
        // $_categories = $this->categoryHelper->getStoreCategories(true,false,true);
        $_categories = $this->getCategoryCollection(true, false, false, false);
        foreach ($_categories as $category){
           $this->setGroup($category);
        }
        $categoryList = $this->categoryList;
        $ret = [];
        foreach($categoryList as $k=>$v){
            $child = [];
            foreach($v as $k1=>$v1){
                $child[] = [
                    'label'=>$v1,
                    'value'=>$k1
                ];
            }
            $level = (int)$k -1;
            $ret[] = [
                'label'=> 'Category level '.$level,
                'value'=>$child
            ];
            
        }
        return $ret;
    }
    
}
?>