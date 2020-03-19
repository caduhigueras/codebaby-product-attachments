<?php
/**
 * CodeBaby_ProductAttachments | ProductFileUploadRepositoryInterface.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 20/2/20
 **/

namespace CodeBaby\ProductAttachments\Api;

use CodeBaby\ProductAttachments\Api\Data\ProductFileUploadInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductFileUploadRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @param $relatedProduct
     * @return mixed
     */
    public function getByRelatedProduct($relatedProduct);

    /**
     * @param ProductFileUploadInterface $productFileUpload
     * @return mixed
     */
    public function save(Data\ProductFileUploadInterface $productFileUpload);

    /**
     * @param ProductFileUploadInterface $productFileUpload
     * @return mixed
     */
    public function delete(Data\ProductFileUploadInterface $productFileUpload);

    /**
     * @param $productId
     * @return mixed
     */
    public function deleteById($productId);
}