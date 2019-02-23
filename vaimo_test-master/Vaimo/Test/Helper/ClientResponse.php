<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 12/10/2018
 * Time: 4:57 AM
 */
namespace Vaimo\Test\Helper;
use Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\HTTP\ZendClientFactory;
use Psr\Log\LoggerInterface;
use \Vaimo\Test\Api\RequestProtocolInterface;

class ClientResponse implements RequestProtocolInterface{

    const MIN_IN_STOCK = 0;
    const VAIMO_ENDPOINT = 'http://server-v894.vaimo.com/__ic/testapi/index.php';
    /** @var ZendClientFactory */
    protected $clientFactory;
    /**
     * @var JsonFactory
     */
    protected $logger;

    public function __construct(ZendClientFactory $clientFactory,
                                LoggerInterface $logger)
    {
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
    }

    public function getResponse($url){
        $client = $this->clientFactory->create();
        try{
            $client->setUri($url);
            $client->setHeaders(['Content-Type: application/json', 'Accept: application/json']);
            $client->setMethod(\Zend_Http_Client::GET);
        }catch (\Exception $exception){
           $this->logger->critical('Unable to make HTTP request');
        }
        $response = $client->request()->getBody();
        $response = json_decode($response, true);
        return $response;
    }
}