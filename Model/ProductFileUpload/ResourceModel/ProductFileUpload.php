<?php
/**
 * CodeBaby_ProductAttachments | ProductFileUpload.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 20/2/20
 **/

namespace CodeBaby\ProductAttachments\Model\ProductFileUpload\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class ProductFileUpload extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(Context $context, EntityManager $entityManager, $connectionName = null)
    {
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('codebaby_product_attachments', 'id');
    }

    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }
}