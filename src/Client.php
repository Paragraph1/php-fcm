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
    private $recipients = array();
    private $recipientType;
    private $guzzleClient;

    public function injectGuzzleHttpClient(GuzzleHttp\Client $client)
    {
        $this->guzzleClient = $client;
    }

    /**
     * where should the message go
     *
     * @param Recipient $recipient
     *
     * @return \paragraph1\phpFCM\Client
     */
    public function addRecipient(Recipient $recipient)
    {
        $this->recipients[] = $recipient;

        if (!isset($this->recipientType)) {
            $this->recipientType = get_class($recipient);
        }
        if ($this->recipientType !== get_class($recipient)) {
            throw new \InvalidArgumentException('mixed recepient types are not supported by FCM');
        }

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
        // TODO
        $to = $this->createTo();
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

    private function createTo()
    {
        switch ($this->recipientType) {
            case Topic::class:
                return implode(
                    ' || ',
                    array_map(function (Topic $topic) { return sprintf("'%s' in topics ", $topic->getName()); }, $this->recipients)
                );
                break;
            default:
                throw new \UnexpectedValueException('currently phpFCM only supports topic messages');
                break;
        }
    }
}