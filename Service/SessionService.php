<?php
/**
 * ChargeAfter
 *
 * @category    Payment Gateway
 * @package     Chargeafter_Payment
 * @copyright   Copyright (c) 2021 ChargeAfter.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      eduard.samoilenko@chargeafter.com
 */

namespace Chargeafter\Payment\Service;

use Chargeafter\Payment\Helper\ApiHelper;
use Exception;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\HTTP\ClientInterface;

class SessionService
{
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ApiHelper
     */
    private $apiHelper;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * SessionService constructor.
     *
     * @param ClientFactory $clientFactory
     * @param StoreManagerInterface $storeManager
     * @param ApiHelper $apiHelper
     */
    public function __construct(
        ClientFactory $clientFactory,
        StoreManagerInterface $storeManager,
        ApiHelper $apiHelper,
        DriverInterface $driver
    ) {
        $this->clientFactory = $clientFactory;
        $this->storeManager = $storeManager;
        $this->apiHelper = $apiHelper;
        $this->driver = $driver;
    }

    /**
     * @return mixed|null
     */
    public function createSession()
    {
        $body['requestInfo'] = ['flowType' => 'Apply', 'channel' => 'e_commerce', 'source' => 'Api'];

        $response = $this->content(
            $this->request('/api/sessions', $body, Request::HTTP_METHOD_POST)
        );

        return is_array($response) && key_exists('id', $response) ? $response['id'] : null;
    }

    /**
     * @param $sessionId
     * @return mixed|null
     */
    public function getMerchantBySession($sessionId)
    {
        $response = $this->content(
            $this->request("/api/sessions/{$sessionId}?projection=MerchantId")
        );

        return is_array($response) && key_exists('data', $response)
                ? ( key_exists('merchantId', $response['data']) ? $response['data']['merchantId'] : null )
                : null;
    }

    /**
     * @param $merchantId
     * @return mixed
     */
    public function getSettingByMerchant($merchantId)
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $uri = $this->apiHelper->getCdnUrl($storeId) . "/assets/merchants/{$merchantId}/settings.json";

            $settings = $this->driver->fileGetContents($uri);
            if (!empty($settings)) {
                $json_settings = json_decode($settings, true);
                if (empty($json_settings)) {
                    //@codingStandardsIgnoreStart
                    $json_settings = json_decode(gzdecode($settings), true);
                    //@codingStandardsIgnoreEnd
                }

                return $json_settings;
            }

            return null;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $uriEndpoint
     * @param array $body
     * @param string $requestMethod
     *
     * @return Response
     */
    private function request(
        string $uriEndpoint,
        array $body = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): Response {
        try {
            /** @var ClientInterface $client */
            $storeId = $this->storeManager->getStore()->getId();
            $uri = $this->apiHelper->getApiUrl($uriEndpoint, $storeId, true);

            $client = $this->clientFactory->create();

            $client->setHeaders([
                'Authorization' => 'Bearer ' . $this->apiHelper->getPublicKey($storeId),
                'Content-Type'  => 'application/json'
            ]);

            $client->setOption(CURLOPT_RETURNTRANSFER, true);

            switch ($requestMethod) {
                case Request::HTTP_METHOD_POST:
                    $client->post($uri, json_encode($body));
                    break;

                default:
                    $client->get($uri);
            }

            $response = new Response($client->getStatus(), $client->getHeaders(), $client->getBody());
        } catch (\Exception $exception) {
            $response = new Response(500, [], null, '1.1', $exception->getMessage());
        }

        return $response;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    private function content(Response $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
