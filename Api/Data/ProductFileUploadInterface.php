<?php
/**
 * CodeBaby_ProductAttachments | ProductFileUploadInterface.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 20/2/20
 **/

namespace CodeBaby\ProductAttachments\Api\Data;

interface ProductFileUploadInterface
{
    const RELATED_PRODUCT_ID = 'id';
    const RELATED_PRODUCT = 'related_product';
    const SERIALIZED_UPLOADED_FILES = 'serialized_uploaded_files';
    const STORE_ID = 'store_id';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getRelatedProduct();

    /**
     * @return mixed
     */
    public function getSerializedUploadedFiles();

    /**
     * @return mixed
     */
    public function getStoreId();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @param $relatedProduct
     * @return mixed
     */
    public function setRelatedProduct($relatedProduct);

    /**
     * @param $serializedUploadedFiles
     * @return mixed
     */
    public function setSerializedUploadedFiles($serializedUploadedFiles);

    /**
     * @param $storeId
     * @return mixed
     */
    public function setStoreId($storeId);
}