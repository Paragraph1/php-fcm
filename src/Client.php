<?php
namespace phpFCM;

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
     * @return \phpFCM\Client
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
     * @return \phpFCM\Client
     */
    public function setProxyApiUrl($url)
    {
        $this->proxyApiUrl = $url;
        return $this;
    }

    public function send(Message $message)
    {
        // TODO
    }
}