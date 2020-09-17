<?php

namespace Gssi\BezdanBrand\Block;

class ImageAttribute extends \Magento\Framework\View\Element\Template {

    /**
     * @var Product
     */
    protected $_product = null;


    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     *
     * @var Magento\Framework\Filesystem\Driver\File 
     */
    protected $driverFile;
    
    /**
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList 
     */
    protected $directory_list;
    
    /**
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     * @param \Magento\Framework\App\ObjectManager $objectManager
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, 
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Filesystem\Driver\File $driverFile, 
            \Magento\Framework\App\Filesystem\DirectoryList $directory_list, 
            \Magento\Framework\ObjectManagerInterface $objectManager,
            array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->driverFile = $driverFile;
        $this->directory_list = $directory_list;
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * 
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct() {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }
    
    public function getLogo() {
        $product = $this->getProduct();
        $brand = $product->getData('bz_brand');
        $serie = $product->getData('bz_series');
        
        if (!$brand && !$serie) {
            return '';
        }
        
        $brandValue = $product->getAttributeText('bz_brand');
        $brandAttribute = $product->getResource()->getAttribute('bz_brand');
        $brandOptionId = $brandAttribute->getSource()->getOptionId($brandValue);
        $brandAdminLabel = $this->getOptionAdminLabel($brandAttribute->getId(), $brandOptionId);
        
        $serieValue = $product->getAttributeText('bz_series');
        $serieAttribute = $product->getResource()->getAttribute('bz_series');
        $serieOptionId = $serieAttribute->getSource()->getOptionId($serieValue);
        $serieAdminLabel = $this->getOptionAdminLabel($serieAttribute->getId(), $serieOptionId);
        
        //return serie logo as the first if it exist
        if (!empty($serieAdminLabel)) {
            $isSerie = $this->checkFileExist($serieAdminLabel, 'bz_series');
            if ($isSerie) {
                return [
                    'src' => $this->_storeManager->getStore()->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA 
                            ) . '/wysiwyg/bz_series/' . $serieAdminLabel . '.png'
                            ,
                    'attributeText' => $serieValue, 
                    'class' => 'bzseries'
                ];
            }
        }
        if (!empty ($brandAdminLabel)) { 
            $isBrand = $this->checkFileExist($brandAdminLabel, 'bz_brand');
            if ($isBrand) {
                $brandLink = '';
                $brandConfig = $this->getConfigBrands();
                if (count($brandConfig)) {
                    foreach ($brandConfig as $_config) {
                        if ($_config['option'] == $brandOptionId) {
                            $brandLink = $this->getUrl($_config['link']);
                        }
                    }
                }
                return [
                    'src' => $this->_storeManager->getStore()->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA 
                            )  . '/wysiwyg/bz_brand/' . $brandAdminLabel . '.png',
                    'attributeText' => $brandValue,
                    'class' => 'bzbrand',
                    'brandLink' => $brandLink
                ];
            }
        }
        
        return false;
    }

    /**
     * 
     * @return []
     */
    protected function getConfigBrands() {
        $tableConfig = $this->_scopeConfig->getValue('bzb/general/active');
        if ($tableConfig) {
            $tableConfigResults = unserialize($tableConfig);
            if (is_array($tableConfigResults)) {
                return $tableConfigResults;
            }
        }
        return [];
    }
    
    /**
     * check the file exists in brand or series folder
     * 
     * @param string $baseName
     * @param string $type
     * @return boolean
     */
    protected function checkFileExist($baseName = null, $type = 'bz_brand') {
        if ($baseName == null) {
            return false;
        }
        
        $baseName .= '.png';
        
        $path = $this->directory_list->getPath('media')
                . DIRECTORY_SEPARATOR . 'wysiwyg'
                . DIRECTORY_SEPARATOR . $type
                . DIRECTORY_SEPARATOR . $baseName;
        
        if ($this->driverFile->isExists($path)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 
     * @param int $attributeId
     * @param int $optionId
     * @return string
     */
    protected function getOptionAdminLabel($attributeId, $optionId) {
        $options = $this->objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection')
                ->setAttributeFilter($attributeId)
                ->setStoreFilter(0)
                ->toOptionArray();
        $adminLabel = '';
        if(count($options)) {
            foreach($options as $_option) {
                if(isset($_option['value']) && $_option['value'] == $optionId) {
                    $adminLabel = str_replace(' ', '', $_option['label']);
                }
            }
        }
        
        return $adminLabel;
    }

}
