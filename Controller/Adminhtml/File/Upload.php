<?php
/**
 * CodeBaby_ProductAttachments | Upload.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 20/2/20
 **/
namespace CodeBaby\ProductAttachments\Controller\Adminhtml\File;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Controller\ResultFactory;
use CodeBaby\ProductAttachments\Model\Uploader\FileProcessor;

class Upload extends Action
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    public function __construct(Action\Context $context, FileProcessor $fileProcessor, ImageUploader $imageUploader)
    {
        $this->fileProcessor = $fileProcessor;
        $this->imageUploader = $imageUploader;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $dirType = \Magento\Framework\App\Filesystem\DirectoryList::MEDIA;
        $dir = 'tmp/code-baby/product-file-uploads';
        $dynamicFile = [];
        $dynamicRows = $this->getRequest()->getFiles('product')['code_baby_product_attachments_fieldset']['product_attachments_field'];
        foreach ($dynamicRows as $dynamicRow) {
            $dynamicFile = $dynamicRow['file'];
        }
        $result = $this->fileProcessor->saveFile($dynamicFile, $dirType, $dir);
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
