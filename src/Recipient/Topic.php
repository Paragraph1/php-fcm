<?php
namespace paragraph1\phpFCM\Recipient;

class Topic implements Recipient
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getIdentifier()
    {
        return $this->name;
    }
}