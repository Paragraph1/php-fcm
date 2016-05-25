<?php
namespace paragraph1\phpFCM;

use GuzzleHttp;

/**
 *
 * @author palbertini
 *
 */
class Client
{
    const DEFAULT_API_URL = 'https://fcm.googleapis.com/fcm/send';

    private $apiKey;
    private $proxyApiUrl;
    private $guzzleClient;

    public function injectGuzzleHttpClient(GuzzleHttp\Client $client)
    {
        $this->guzzleClient = $client;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * people can overwrite the api url with a proxy server url of their own
     *
     * @param unknown $url
     *
     * @return \paragraph1\phpFCM\Client
     */
    public function setProxyApiUrl($url)
    {
        $this->proxyApiUrl = $url;
        return $this;
    }

    public function send(Message $message)
    {
        $this->guzzleClient->post(
            $this->getApiUrl(),
            [
                'headers' => [
                    'Authorization' => sprintf('key=%s', $this->apiKey),
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($message)
            ]
        );
    }

    public function getApiUrl()
    {
        return isset($this->proxyApiUrl) ? $this->proxyApiUrl : self::DEFAULT_API_URL;
    }
}