<?php

namespace CodeBaby\ProductAttachments\Model\Config\Source;

class LayoutTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Show as Rows')], ['value' => 2, 'label' => __('Show as Columns')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Show as Rows'), 2 => __('Show as Columns')];
    }
}
