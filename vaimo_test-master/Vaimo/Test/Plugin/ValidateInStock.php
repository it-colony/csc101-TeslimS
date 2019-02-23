<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/10/2018
 * Time: 8:30 AM
 */
namespace Vaimo\Test\Plugin;
use Magento\Store\Model\StoreManagerInterface;
use Vaimo\Test\Api\RequestProtocolInterface;
class ValidateInStock
{

    protected $storeManagerInterface;
    protected $responseFactory;

    public function __construct(
        StoreManagerInterface $storeManager,
        RequestProtocolInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->storeManagerInterface = $storeManager;
    }

    public function aroundValidate(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $subject,
        \Closure $proceed,
        \Magento\Framework\Event\Observer $observer)
    {
        /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getItem();
        $sku = $quoteItem->getSku();
        $url = $this->storeManagerInterface->getStore()->getBaseUrl().'endpoint/page/action?sku='.$sku;
        $response = $this->responseFactory->getResponse($url);
        $inStock = $response[0]["instock"];

        if ($inStock == \Vaimo\Test\Helper\ClientResponse::MIN_IN_STOCK) {
            $quoteItem->addErrorInfo(
                'cataloginventory',
                \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                __('This product is not in our stock')
            );
            $quoteItem->getQuote()->addErrorInfo(
                'stock',
                'cataloginventory',
                \Magento\CatalogInventory\Helper\Data::ERROR_QTY,
                __('Some of the products are not in stock.')
            );

            return;
        }

        return $proceed($observer);
    }
}