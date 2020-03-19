<?php
/**
 * CodeBaby_ProductAttachments | ProductEvents.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 19/2/20
 **/
namespace CodeBaby\ProductAttachments\Model\Uploader;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\Io\File as FileIo;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;

class FileProcessor
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var UploaderFactory
     */
    protected $fileUploader;

    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var File
     */
    private $_file;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var FileIo
     */
    private $filesystemIo;

    /**
     * FileProcessor constructor.
     * @param ManagerInterface $messageManager
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploader
     * @param ResourceConnection $resource
     * @param File $file
     * @param FileIo $filesystemIo
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ManagerInterface $messageManager,
        Filesystem $filesystem,
        UploaderFactory $fileUploader,
        ResourceConnection $resource,
        File $file,
        FileIo $filesystemIo,
        UrlInterface $urlBuilder
    ) {
        $this->messageManager = $messageManager;
        $this->filesystem = $filesystem;
        $this->fileUploader = $fileUploader;
        $this->resource = $resource;
        $this->_file = $file;
        $this->urlBuilder = $urlBuilder;
        $this->filesystemIo = $filesystemIo;
    }

    /**
     * upload files to $dirType/$dir and return the file as an array
     * @param $file array
     * @param $dirType string from \Magento\Framework\App\Filesystem\DirectoryList
     * @param $dir
     * @param $allowedTypes null|array
     * @return array|bool
     */
    public function saveFile($file, $dirType, $dir, $allowedTypes = null)
    {
        try {
            $mediaDir = $this->filesystem->getDirectoryWrite($dirType);
            $target = $mediaDir->getAbsolutePath($dir);

            /**
             * $fileField correspond to the field name of the form that's being submitted
             * check: $_POST['param_name']
             * @var $uploader \Magento\MediaStorage\Model\File\Uploader
             */
            $uploader = $this->fileUploader->create(['fileId' => $file]);

            // set allowed file extensions
            if ($allowedTypes) {
                $uploader->setAllowedExtensions($allowedTypes);
            }

            // allow folder creation
            $uploader->setAllowCreateFolders(true);

            // rename file name if already exists
            $uploader->setAllowRenameFiles(true);

            // upload file in the specified folder
            $result = $uploader->save($target);

            //TODO: change response message to ajax?
            //if ($result['file']) {
            //$this->messageManager->addSuccessMessage(__('File has been successfully uploaded.'));
            //}
            $result['url'] = $this->urlBuilder->getBaseUrl() . $dirType . '/' . $dir . '/' . $result['file'];
            return $result;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return false;
    }

    /**
     * used to move images saved from media/tmp folder to actual folder when updating a given form
     * @param $image array
     * @param $dir string
     * @param $dirType string should be a constant present @\Magento\Framework\App\Filesystem\DirectoryList
     * @return boolean|array
     * @throws FileSystemException
     */
    public function mvImgFromTmp($image, $dir, $dirType)
    {
        $mediaFolder = $this->filesystem->getDirectoryWrite($dirType);
        $destinationFolder = $mediaFolder->getAbsolutePath() . $dir;
        try {
            //if destination folder do not exist, create it
            $this->filesystemIo->checkAndCreateFolder($destinationFolder);
        } catch (\Exception $e) {
            //TODO: add logger
            return false;
        }
        $filePath = $image[0]['path'] . '/' . $image[0]['file'];
        $destinationPath = $destinationFolder . '/' . $image[0]['file'];
        //if it is tmp, let's move file to proper folder and delete temp one
        $this->filesystemIo->mv($filePath, $destinationPath);
        $image[0]['url'] = '/' . $dirType . '/' . $dir . '/' . $image[0]['file'];
        $image[0]['name'] = $image[0]['file'];

        //removing unnecessary information
        $els = ['tmp_name', 'error', 'file', 'path'];
        foreach ($els as $el) {
            unset($image[0][$el]);
        }
        return $image;
    }

    /**
     * @param $fileName
     * @param $dir
     * @param $dirType
     * @return bool
     * @throws FileSystemException
     */
    public function rmFile($fileName, $dir, $dirType)
    {
        $mainDir = $this->filesystem->getDirectoryWrite($dirType);
        $target = $mainDir->getAbsolutePath($dir);

        try {
            if ($this->_file->isExists($target . '/' . $fileName)) {
                $this->_file->deleteFile($target . '/' . $fileName);
                return true;
            }
        } catch (FileSystemException $e) {
            //TODO: add custom logger
            throw new FileSystemException(__($e->getMessage()));
        }
    }
}