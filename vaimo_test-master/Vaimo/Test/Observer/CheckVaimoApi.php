<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/8/2018
 * Time: 10:05 PM
 */

namespace Vaimo\Test\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Vaimo\Test\Api\RequestProtocolInterface;

class CheckVaimoApi implements ObserverInterface{

    protected $storeManagerInterface;
    protected $responseFactory;

    public function __construct(
        StoreManagerInterface $storeManager,
        RequestProtocolInterface $responseFactory )
    {
        $this->responseFactory = $responseFactory;
        $this->storeManagerInterface = $storeManager;
    }

    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        $allItems = $quote->getAllVisibleItems();

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($allItems as $item){
            $sku = $item->getSku();
            $url = $this->storeManagerInterface->getStore()->getBaseUrl().'endpoint/page/action?sku='.$sku;
            $response = $this->responseFactory->getResponse($url);
            $inStock = $response[0]["instock"];
            if ($inStock == \Vaimo\Test\Helper\ClientResponse::MIN_IN_STOCK)
                exit;
        }

    }
}