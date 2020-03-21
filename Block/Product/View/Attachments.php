<?php

namespace CodeBaby\ProductAttachments\Block\Product\View;

use CodeBaby\ProductAttachments\Api\ProductFileUploadRepositoryInterface;
use Magento\Framework\Serialize\Serializer\JsonFactory as JsonSerializer;
use Magento\Framework\View\Element\Template;

class Attachments extends Template
{
    /**
     * @var Template\Context
     */
    private $context;
    /**
     * @var ProductFileUploadRepositoryInterface
     */
    private $productFileUploadRepository;
    /**
     * @var JsonSerializer
     */
    private $serializer;

    public function __construct(
        JsonSerializer $serializer,
        ProductFileUploadRepositoryInterface $productFileUploadRepository,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context = $context;
        $this->productFileUploadRepository = $productFileUploadRepository;
        $this->serializer = $serializer->create();
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    public function getStoreId()
    {
        return $this->context->getStoreManager()->getStore()->getId();
    }

    public function getProductAttachments()
    {
        $productAttachments = $this->productFileUploadRepository->getByRelatedProduct($this->getProductId());
        if ($productAttachments->getStoreId() === "0" || $productAttachments->getStoreId() === $this->getStoreId()) {
            return $this->serializer->unserialize($productAttachments->getSerializedUploadedFiles());
        }
        return false;
    }

    public function getFileIcon($productName)
    {
        $icons = ['png', 'jpg', 'jpeg', 'doc', 'pdf', 'xls', 'xlsx', 'csv', 'avi', 'mkv', 'mp4', 'zip', 'txt'];
        $extensionArr = explode('.', $productName);
        $extension = array_pop($extensionArr);
        if (!in_array($extension, $icons)) {
            return 'generic';
        } else {
            if (strtolower($extension) === 'jpeg' ) {
                return 'jpg';
            } elseif (strtolower($extension) === 'xlsx' ) {
                return 'xls';
            }
            return strtolower($extension);
        }
    }

}