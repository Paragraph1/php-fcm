<?php
namespace paragraph1\phpFCM;

class Notification extends Message
{
    private $title;
    private $body;
    private $badge;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }


    public function setBadge($badge)
    {
        $this->badge = $badge;
        return $this;
    }

    public function jsonSerialize()
    {
        $jsonData = array(
            'title' => $this->title,
            'body' => $this->body
        );
        if ($this->badge) {
            $jsonData['badge'] = $this->badge;
        }
        return $jsonData;
    }
}