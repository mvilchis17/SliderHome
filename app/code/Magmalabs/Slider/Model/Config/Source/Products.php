<?php

namespace Magmalabs\Slider\Model\Config\Source; 

use Magento\Framework\Option\ArrayInterface;

class Products implements ArrayInterface
{

    const NEW_PRODUCTS = 'new_products';
    const BESTSELLERS  = 'bestsellers';
    const MORE_VIEWS   = 'more_views';

    public function toOptionArray(){

        return array(
            array('value' => self::BESTSELLERS, 'label' => 'Bestsellers'),
            array('value' => self::NEW_PRODUCTS, 'label' => 'New Products'),
            array('value' => self::MORE_VIEWS, 'label' => 'More Views'),
        );
    }

}

