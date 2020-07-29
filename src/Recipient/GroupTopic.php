<?php
namespace paragraph1\phpFCM\Recipient;

class GroupTopic implements Recipient
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getIdentifier()
    {
        return $this->name;
    }
}