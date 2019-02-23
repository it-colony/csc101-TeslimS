<?php

namespace Vaimo\Test\Controller\Page;


use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Vaimo\Test\Api\RequestProtocolInterface;


class Action extends \Magento\Framework\App\Action\Action
{

    protected $jsonFactory;

    protected $responseFactory;

    /**
     * Action constructor.
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Vaimo\Test\Api\RequestProtocolInterface $responseFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\App\Action\Context $context,
        RequestProtocolInterface $responseFactory)
    {
        $this->jsonFactory = $jsonFactory;
        $this->responseFactory = $responseFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $sku = $this->getRequest()->getParam('sku', "");
        $response = $this->responseFactory->getResponse(\Vaimo\Test\Helper\ClientResponse::VAIMO_ENDPOINT.'?sku='.$sku);
        return $this->jsonFactory->create()->setData(($response));

    }

}