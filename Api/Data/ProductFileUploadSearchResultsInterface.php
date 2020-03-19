<?php
/**
 * CodeBaby_MasterAccount | NegotiatedPricesSearchResultsInterface.php
 * Created by CodeBaby DevTeam.
 * User: c.dias
 * Date: 09/3/20
 **/
namespace CodeBaby\ProductAttachments\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ProductFileUploadSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list.
     *
     * @return ProductFileUploadInterface[]
     */
    public function getItems();

    /**
     * Set blocks list.
     *
     * @param ProductFileUploadInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
