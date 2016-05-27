<?php
namespace paragraph1\phpFCM;

class Notification extends Message
{
    private $title;
    private $body;
    private $badge;
    private $icon;
    private $clickAction;

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

    /**
     * iOS only, will add smal red bubbles indicating the number of notifications to your apps icon
     *
     * @param integer $badge
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
        return $this;
    }

    /**
     * android only, set the name of your drawable resource as string
     *
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    public function setClickAction($actionName)
    {
        $this->clickAction = $actionName;
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
        if ($this->icon) {
            $jsonData['icon'] = $this->icon;
        }
        if ($this->clickAction) {
            $jsonData['click_action'] = $this->clickAction;
        }
        return $jsonData;
    }
}