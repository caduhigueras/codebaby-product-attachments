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

    /**
     * Attachments constructor.
     * @param JsonSerializer $serializer
     * @param ProductFileUploadRepositoryInterface $productFileUploadRepository
     * @param Template\Context $context
     * @param array $data
     */
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

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->context->getStoreManager()->getStore()->getId();
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductAttachments()
    {
        $productAttachments = $this->productFileUploadRepository->getByRelatedProduct($this->getProductId());
        if ($productAttachments->getStoreId() === "0" || $productAttachments->getStoreId() === $this->getStoreId()) {
            return $this->serializer->unserialize($productAttachments->getSerializedUploadedFiles());
        }
        return false;
    }

    /**
     * @param $productName
     * @return string
     */
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

    /**
     * 1- rows | 2- columns
     * @return string
     */
    public function getCustomLayout()
    {
        $layout = $this->context->getScopeConfig()->getValue('product_attachments/attachment_settings/custom_layout');
        if ($layout === '1') {
            return 'rows-layout';
        } else {
            return 'columns-layout';
        }
    }

}