<?php
namespace phpFCM;

/**
 *
 * @author palbertini
 *
 */
class Client
{
    private $_apiKey;
    private $_apiUrl = 'https://fcm.googleapis.com/fcm/send';
    private $_recipients = array();

    /**
     * where should the message go
     *
     * @param Recipient $recipient
     *
     * @return \phpFCM\Client
     */
    public function addRecipient(Recipient $recipient)
    {
        $this->_recipients[] = $recipient;
        return $this;
    }

    /**
     * people can overwrite the api url with a proxy server url of their own
     *
     * @param unknown $url
     *
     * @return \phpFCM\Client
     */
    public function setApiUrl($url)
    {
        $this->_apiKey = $url;
        return $this;
    }
}