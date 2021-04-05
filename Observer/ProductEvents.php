<?php
/**
 * CodeBaby_ProductAttachments | ProductEvents.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 19/2/20
 **/

namespace CodeBaby\ProductAttachments\Observer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\JsonFactory as JsonSerializer;
use CodeBaby\ProductAttachments\Api\ProductFileUploadRepositoryInterface;
use CodeBaby\ProductAttachments\Model\ProductFileUpload\ProductFileUploadFactory;
use CodeBaby\ProductAttachments\Model\Uploader\FileProcessor;

class ProductEvents implements ObserverInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $_request;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var JsonSerializer
     */
    private $json;

    /**
     * @var ProductFileUploadRepositoryInterface
     */
    private $productFileUploadRepository;
    /**
     * @var ProductFileUploadFactory
     */
    private $productFileUploadFactory;

    public function __construct(
        Context $context,
        FileProcessor $fileProcessor,
        JsonSerializer $json,
        ProductFileUploadRepositoryInterface $productFileUploadRepository,
        ProductFileUploadFactory $productFileUploadFactory
    ) {
        $this->context = $context;
        $this->_request = $context->getRequest();
        $this->fileProcessor = $fileProcessor;
        $this->json = $json->create();
        $this->productFileUploadRepository = $productFileUploadRepository;
        $this->productFileUploadFactory = $productFileUploadFactory->create();
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute(Observer $observer)
    {
        $params = $this->_request->getParams();
        //check if there are any files uploaded
        if (isset($params['product']['code_baby_product_attachments_fieldset']['product_attachments_field'])) {
            $customFieldData = $params['product']['code_baby_product_attachments_fieldset']['product_attachments_field'];
            $previousFileUpload = $this->fileUploadExists($params['id']);
            if ($previousFileUpload) {
                $fileDif = $this->searchForDifferences($previousFileUpload, $customFieldData);
                if ($fileDif) {
                    $this->updateFileUpload($previousFileUpload->getId(), $fileDif);
                }
            } else {
                $this->saveFileUploadRelation($customFieldData, $params['id']);
            }
            //if upload input is empty, check if file uploads existed before and delete them
        } else {
            $fileUploads = $this->fileUploadExists($params['id']);
            if ($fileUploads) {
                $this->deleteFileUpload($fileUploads);
            }
        }
    }

    /**
     * @param $customFieldData
     * @param $productId
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function saveFileUploadRelation($customFieldData, $productId)
    {
        $dirType = DirectoryList::MEDIA;
        $dir = 'code-baby/product-file-uploads/' . $productId;
        $productUploadsFinalDir = [];
        //$productUploadsFinalDir['store_id'] = $data['store_id'];
        //$productUploadsFinalDir['related_product'] = $data['related_product'];
        $finalDirFiles = [];
        foreach ($customFieldData as $productUpload) {
            $newDestProductUpload = $this->fileProcessor->mvImgFromTmp($productUpload['file'], $dir, $dirType);
            $finalDirFiles['record_id'] = $productUpload['record_id'];
            $finalDirFiles['file_title'] = $productUpload['file_title'];
            $finalDirFiles['file_external_url'] = isset($productUpload['file_external_url']) ? $productUpload['file_external_url'] : null;
            $finalDirFiles['file_select_type'] = isset($productUpload['file_select_type']) ? $productUpload['file_select_type'] : null;
            $finalDirFiles['file'] = $newDestProductUpload;
            $finalDirFiles['initialize'] = "true";
            array_push($productUploadsFinalDir, $finalDirFiles);
        }
        //$productUploadsFinalDir['serialized_uploaded_files'] = $this->json->serialize($finalDirFiles);
        $data = [
            'store_id' => '0',
            'related_product' => $productId,
            'serialized_uploaded_files' => $this->json->serialize($productUploadsFinalDir)
        ];
        $productUpload = $this->productFileUploadFactory->setData($data);
        $this->productFileUploadRepository->save($productUpload);
//        $this->fileProcessor->mvImgFromTmp();
    }

    /**
     * @param $productId
     * @return mixed
     */
    public function fileUploadExists($productId)
    {
        return $this->productFileUploadRepository->getByRelatedProduct($productId);
    }

    /**
     * @param $currentData
     * @param $newData
     * @return array|bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function searchForDifferences($currentData, $newData)
    {
        $currToSave = [];
        $dirType = DirectoryList::MEDIA;
        $dir = 'codebaby/product-file-uploads/' . $currentData->getRelatedProduct();
        $curr = $this->json->unserialize($currentData->getSerializedUploadedFiles());
        //if they are the same, returns false - no diff
        if ($curr === $newData) {
            return false;
        }
        //first we check if there are any deleted file items and remove them from the dir
        foreach ($curr as $file) {
            $deleted = true;
            foreach ($newData as $data) {
                if ($data['file'] === $file['file']) {
                    $deleted = false;
                }
            }
            if ($deleted) {
                $this->fileProcessor->rmFile($file['file'][0]['name'], $dir, $dirType);
            }
        }
        //now we check for new file items and move them to dir
        foreach ($newData as $key => $file) {
            if (isset($file['file'][0]['path'])) {
                $newData[$key]['file'] = $this->fileProcessor->mvImgFromTmp($file['file'], $dir, $dirType);
            }
        }
        return $newData;
    }

    /**
     * @param $searchThis
     * @param $searchIn
     * @return array|bool
     */
    public function findAddedInArray($searchThis)
    {
        $test = $searchThis;
        foreach ($searchThis as $item) {
            if ($searchThis === $item) {
                //if item was found means it wasn't either deleted or added, so we have nothing to do
                return false;
            } else {
                return $searchThis;
//                array_push($difItems, $searchThis);
            }
        }
//        return $difItems;
    }

    /**
     * @param $searchThis
     * @param $searchIn
     * @return bool
     */
    public function findDeletedInArray($searchThis, $searchIn)
    {
//        $difItems = [];
        foreach ($searchIn as $item) {
            if ($searchThis === $item['file']) {
                //if item was found means it wasn't either deleted or added, so we have nothing to do
                return false;
            } else {
                return $searchThis;
//                array_push($difItems, $searchThis);
            }
        }
//        return $difItems;
    }

    /**
     * @param $productUpload
     */
    public function deleteFileUpload($productUpload)
    {
        $this->productFileUploadRepository->deleteById($productUpload->getId());
        //must remove physical file also
    }

    /**
     * @param $productUploadId
     * @param $fileDif
     */
    public function updateFileUpload($productUploadId, $fileDif)
    {
        $productUpload = $this->productFileUploadRepository->getById($productUploadId);
        $newData = [];
        foreach ($fileDif as $file) {
            $data = [];
            $data['record_id'] = $file['record_id'];
            $data['file_title'] = $file['file_title'];
            $data['file_external_url'] = isset($file['file_external_url']) ? $file['file_external_url'] : null;
            $data['file_select_type'] = isset($file['file_select_type']) ? $file['file_select_type'] : null;
            $data['file'] = $file['file'];
            $data['initialize'] = "true";
            array_push($newData, $data);
        }
        $productUpload->setSerializedUploadedFiles($this->json->serialize($newData));
        $this->productFileUploadRepository->save($productUpload);
    }
}
