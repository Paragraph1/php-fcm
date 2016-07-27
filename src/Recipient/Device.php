<?php
namespace paragraph1\phpFCM\Recipient;

class Device implements Recipient
{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getIdentifier()
    {
        return $this->token;
    }
}