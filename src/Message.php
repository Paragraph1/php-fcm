<?php
namespace paragraph1\phpFCM;

/**
 * @author palbertini
 *
 */
class Message
{

    private $notification;
    private $collapseKey;
    private $recipient;
    private $priority;
    private $data;


    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;
        return $this;
    }

    public function setCollapseKey($collapseKey)
    {
        $this->collapseKey = $collapseKey;
        return $this;
    }

    public function setRecipient(Recipient $recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function toJson()
    {
        if ($this->recipient) {
            $json['to'] = $this->recipient->toJson();
        }
        if ($this->collapseKey) {
            $json['collapse_key'] = $this->collapseKey;
        }
        if ($this->data) {
            $json['data'] = json_encode($data);
        }
        if ($this->priority) {
            $json['priority'] = $this->priority;
        }
        if ($this->notification) {
            $json['notification'] = $this->notification->toJson();
        }

        return json_encode($json);
    }

}