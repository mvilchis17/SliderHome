<?php

namespace Magmalabs\Slider\Block\Index;


class Index extends \Magento\Framework\View\Element\Template {

    const SLIDER_ACTIVE  = 'slider_home_products/options/active';
    const SLIDER_TYPE    = 'slider_home_products/options/slider_type';
    const SLIDER_NUMBER  = 'slider_home_products/options/number_products';
    const NUMBER_DEFAULT = 5;

    protected $_scopeConfig;
    protected $_resourceConnection;
    protected $_productloader;
    protected $_imageHelper;
    protected $_pricingHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []) {

        $this->_scopeConfig = $scopeConfig;
        $this->_resourceConnection = $resourceConnection;
        $this->_productloader = $productloader;
        $this->_imageHelper = $imageHelper;
        $this->_pricingHelper = $pricingHelper;
        parent::__construct($context, $data);

    }


    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getProducts()
    {

        $storeScope      = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isActive        = $this->_scopeConfig->getValue(self::SLIDER_ACTIVE, $storeScope);
        $typeSlider      = $this->_scopeConfig->getValue(self::SLIDER_TYPE, $storeScope);
        $numberProducts  = $this->_scopeConfig->getValue(self::SLIDER_NUMBER, $storeScope);
        $numberDefault   = self::NUMBER_DEFAULT;
        
        if(!$isActive){
            return false;
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/debug.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('[typeSlider]'.$typeSlider);
        $logger->info('[PRODUCTOS]');
        $logger->info('[LIMIT]'.$numberDefault);
        $imagewidth = 200;
        $imageheight = 200;

        $connection = $this->_resourceConnection->getConnection();
         
        if($numberProducts != ''){
            $limit = $numberProducts;
        }else{
            $limit = $numberDefault;
        }

        $sliderName = '';
        if($typeSlider == 'new_products'){
            $sliderName = 'News Products';
            $sql = "Select entity_id from catalog_product_entity order by created_at desc limit {$limit}";
        }else if($typeSlider == 'bestsellers'){
            $sliderName = 'Bestsellers';
            $sql = "select product_id from sales_bestsellers_aggregated_daily 
                        group by product_id order by period desc limit {$limit}";
        }else if($typeSlider == 'more_views'){
            $sliderName = 'More Views';
            $sql = "select product_id from report_viewed_product_aggregated_daily 
                        group by product_id order by period desc limit {$limit}";
        }

        $logger->info('[$sql]'. $sql);
        
        $result = $connection->fetchAll($sql);
        $logger->info(print_r($result,true));

        $products = array();
        $resultData   = array();
        
        foreach($result as $item){
            
            if($typeSlider == 'new_products'){
                $productId = $item['entity_id'];
            }else{
                $productId = $item['product_id'];
            }

            $modelProduct = $this->_productloader->create()->load($productId);  
            $image_url = $this->_imageHelper->init($modelProduct, 'product_page_image_small')->setImageFile($modelProduct->getFile())->resize($imagewidth, $imageheight)->getUrl();
            
            $products[$productId]['name']      = $modelProduct->getName();
            $products[$productId]['price']     = $this->_pricingHelper->currency($modelProduct->getPrice(), true, false);
            $products[$productId]['url']       = $modelProduct->getProductUrl();
            $products[$productId]['image_url'] = $image_url;
            
        }

        $logger->info(print_r($products,true));  

        $resultData['products']   = $products;
        $resultData['slider_name'] = $sliderName;
        

        return $resultData;

    }

}