<?php
namespace Webindiainc\Subscribe\Model\Config\Source;
class DiscountType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    { 
        return [
            ['value' => 'by_percent', 'label' => __('Percent of product price discount')],
            ['value' => 'by_fixed', 'label' => __('Fixed amount discount')],
            ['value' => 'cart_fixed', 'label' => __('Fixed amount discount for whole cart')],
            ['value' => 'buy_x_get_y', 'label' => __('Buy X get Y free (discount amount is Y)')],
        ];
    }
}
