<?php
/**
 * CodeBaby_ProductAttachments | ProductFileUploadRepository.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 20/2/20
 **/
namespace CodeBaby\ProductAttachments\Model\ProductFileUpload;

use CodeBaby\ProductAttachments\Api\Data\ProductFileUploadInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use CodeBaby\ProductAttachments\Api\Data;
use CodeBaby\ProductAttachments\Api\ProductFileUploadRepositoryInterface;
use CodeBaby\ProductAttachments\Model\ProductFileUpload\ResourceModel\ProductFileUpload as ProductFileUploadResource;
use CodeBaby\ProductAttachments\Model\ProductFileUpload\ResourceModel\ProductFileUpload\CollectionFactory;

class ProductFileUploadRepository implements ProductFileUploadRepositoryInterface
{
    /**
     * @var ProductFileUploadResource
     */
    protected $resource;

    /**
     * @var ProductFileUploadFactory
     */
    protected $productFileUploadFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var Data\ProductFileUploadSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilder;

    /**
     * ProductFileUploadRepository constructor.
     * @param ProductFileUploadFactory $productFileUploadFactory
     * @param CollectionFactory $collectionFactory
     * @param ProductFileUploadResource $productFileUploadResource
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $timezone
     * @param Data\ProductFileUploadSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilder
     */
    public function __construct(
        ProductFileUploadFactory $productFileUploadFactory,
        CollectionFactory $collectionFactory,
        ProductFileUploadResource $productFileUploadResource,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone,
        Data\ProductFileUploadSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilderFactory $searchCriteriaBuilder
    ) {
        $this->resource = $productFileUploadResource;
        $this->productFileUploadFactory = $productFileUploadFactory;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder->create();
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $productFileUpload = $this->productFileUploadFactory->create();
        $this->resource->load($productFileUpload, $id);
        if (!$productFileUpload->getId()) {
            throw new NoSuchEntityException(__('The Product Uploaded Files with the "%1" ID doesn\'t exist.', $id));
        }
        return $productFileUpload;
    }

    /**
     * @param $relatedProduct
     * @return bool|mixed
     * @throws NoSuchEntityException
     */
    public function getByRelatedProduct($relatedProduct)
    {
        //TODO: implement store search too
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('related_product', ['eq' => $relatedProduct]);
        $collection->addFieldToFilter('store_id', ['eq' => $this->storeManager->getStore()->getId()]);
        $item = $collection->getFirstItem();
        if ($item->getId()) {
            return $item;
        }
        return false;
    }

    /**
     * @param ProductFileUploadInterface $productFileUpload
     * @return ProductFileUploadInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(ProductFileUploadInterface $productFileUpload)
    {
        if (empty($productFileUpload->getStoreId())) {
            $productFileUpload->setStoreId($this->storeManager->getStore()->getId());
        }
        try {
            $this->resource->save($productFileUpload);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $productFileUpload;
    }

    /**
     * @param ProductFileUploadInterface $productFileUpload
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ProductFileUploadInterface $productFileUpload)
    {
        try {
            $this->resource->delete($productFileUpload);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param $productId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($productId)
    {
        return $this->delete($this->getById($productId));
    }
}
