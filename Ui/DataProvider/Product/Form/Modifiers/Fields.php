<?php
/**
 * CodeBaby_ProductAttachments | Fields.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 19/2/20
 **/
namespace CodeBaby\ProductAttachments\Ui\DataProvider\Product\Form\Modifiers;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Serialize\Serializer\JsonFactory as JsonSerializer;
use CodeBaby\ProductAttachments\Api\ProductFileUploadRepositoryInterface;

//use Magento\Ui\Component\Form\Fieldset;
//use Magento\Ui\Component\Form\Field;
//use Magento\Ui\Component\Form\Element\Select;
//use Magento\Ui\Component\Form\Element\Input;
//use Magento\Ui\Component\Form\Element\DataType\Text;

class Fields extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;
    /**
     * @var ProductFileUploadRepositoryInterface
     */
    private $productFileUploadRepository;
    /**
     * @var JsonSerializer
     */
    private $json;

    public function __construct(
        LocatorInterface $locator,
        ProductFileUploadRepositoryInterface $productFileUploadRepository,
        JsonSerializer $json
    ) {
        $this->locator = $locator;
        $this->productFileUploadRepository = $productFileUploadRepository;
        $this->json = $json->create();
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    public function modifyData(array $data)
    {
        $product   = $this->locator->getProduct();
        $productId = $product->getId();
        $productFileUpload = $this->productFileUploadRepository->getByRelatedProduct($productId);
        if ($productFileUpload) {
            $data[strval($productId)]['product']['code_baby_product_attachments_fieldset']['product_attachments_field'] = $this->json->unserialize($productFileUpload->getSerializedUploadedFiles());
        }
        return $data;
    }
}