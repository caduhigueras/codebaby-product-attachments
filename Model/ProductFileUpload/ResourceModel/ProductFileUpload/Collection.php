<?php
/**
 * CodeBaby_ProductAttachments | Collection.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 20/2/20
 **/

namespace CodeBaby\ProductAttachments\Model\ProductFileUpload\ResourceModel\ProductFileUpload;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use CodeBaby\ProductAttachments\Model\ProductFileUpload\ProductFileUpload;
use CodeBaby\ProductAttachments\Model\ProductFileUpload\ResourceModel\ProductFileUpload as ProductFileUploadResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ProductFileUpload::class, ProductFileUploadResource::class);
    }
}