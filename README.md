# codebaby-product-attachments
Add field for product attachments so customers can download on product page (Magento 2 Module)

# Install instructions:

Install it via composer: 

`composer require codebaby/product-attachments`

After installation, run the following commands:

`bin/magento s:up`

`bin/magento s:d:c`

`bin/magento s:s:d`
(if on production mode)

`bin/magento s:s:d -f`
(if on developer mode)

`bin/magento c:f`

# Display Files on Product Page:
To display the files, go to `Store > Configuration > CodeBaby Settings > Product_Attachments` and enable display on the desired position

#Display in custom location using xml:
Add this block to the desired xml node:

```xml
<block class="CodeBaby\ProductAttachments\Block\Product\View\Attachments" name="cb.product.files.tab"  template="CodeBaby_ProductAttachments::product/view/product_attachments_tab.phtml" group="detailed_info">
    <arguments>
        <argument translate="true" name="title" xsi:type="string">Product Attachments</argument>
        <argument name="sort_order" xsi:type="string">100</argument>
    </arguments>
</block>
``
