<?php
namespace phpFCM;

class Topic extends Recipient
{
    private $_name;

    public function __construct($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }
}