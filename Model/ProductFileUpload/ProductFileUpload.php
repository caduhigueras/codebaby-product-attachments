<?php
/**
 * CodeBaby_ProductAttachments | ProductFileUpload.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 20/2/20
 **/
namespace CodeBaby\ProductAttachments\Model\ProductFileUpload;

use Magento\Framework\Model\AbstractModel;
use CodeBaby\ProductAttachments\Api\Data\ProductFileUploadInterface;

class ProductFileUpload extends AbstractModel implements ProductFileUploadInterface
{
    protected $_eventPrefix = 'CodeBaby_ProductAttachments_product_file_upload';

    protected function _construct()
    {
        $this->_init(\CodeBaby\ProductAttachments\Model\ProductFileUpload\ResourceModel\ProductFileUpload::class);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getData(self::RELATED_PRODUCT_ID);
    }

    /**
     * @return mixed
     */
    public function getRelatedProduct()
    {
        return $this->getData(self::RELATED_PRODUCT);
    }

    /**
     * @return mixed
     */
    public function getSerializedUploadedFiles()
    {
        return $this->getData(self::SERIALIZED_UPLOADED_FILES);
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @param mixed $id
     * @return ProductFileUpload
     */
    public function setId($id)
    {
        return $this->setData(self::RELATED_PRODUCT_ID, $id);
    }

    /**
     * @param $relatedProduct
     * @return mixed|ProductFileUpload
     */
    public function setRelatedProduct($relatedProduct)
    {
        return $this->setData(self::RELATED_PRODUCT, $relatedProduct);
    }

    /**
     * @param $serializedUploadedFiles
     * @return mixed|ProductFileUpload
     */
    public function setSerializedUploadedFiles($serializedUploadedFiles)
    {
        return $this->setData(self::SERIALIZED_UPLOADED_FILES, $serializedUploadedFiles);
    }

    /**
     * @param $storeId
     * @return mixed|ProductFileUpload
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}