<?php
/**
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      taras@lagan.com.ua
 */

namespace Chargeafter\Payment\Gateway\Http;

use Laminas\Http\Client\Exception\RuntimeException;
use Laminas\Http\Request;
use Laminas\Http\ClientFactory as LaminasClientFactory;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;

use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class Client implements ClientInterface
{
    /**
     * @var LaminasClientFactory
     */
    private $httpClientFactory;

    /**
     * @var ConverterInterface | null
     */
    private $converter;

    /**
     * Client constructor.
     * @param LaminasClientFactory $httpClientFactory
     * @param ConverterInterface|null $converter
     */
    public function __construct(
        LaminasClientFactory $httpClientFactory,
        ConverterInterface   $converter = null
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->converter = $converter;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array
     * @throws ClientException
     * @throws ConverterException
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        $client = $this->httpClientFactory->create();
        $client->setUri($transferObject->getUri());

        $headers = $transferObject->getHeaders();
        if ($headers && key_exists('accept', $headers)) {
            $client->setEncType($headers['accept']);
        }

        if ($transferObject->getMethod() === Request::METHOD_POST && $transferObject->getBody()) {
            $client->setRawBody(json_encode($transferObject->getBody()));
        }

        $client->setHeaders($headers);
        $client->setMethod($transferObject->getMethod());

        try {
            $response = $client->send();

            $result = $this->converter
                ? $this->converter->convert($response->getBody())
                : json_decode($response->getBody(), true);
        } catch (RuntimeException $e) {
            throw new ClientException(
                __($e->getMessage())
            );
        } catch (ConverterException $e) {
            throw $e;
        }

        return $result;
    }
}
