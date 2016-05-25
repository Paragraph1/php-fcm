<?php
namespace paragraph1\phpFCM;

/**
 * @author palbertini
 *
 */
class Message implements \JsonSerializable
{
    private $notification;
    private $collapseKey;
    private $priority;
    private $data;
    private $recipients = array();
    private $recipientType;

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

    public function jsonSerialize()
    {
        $jsonData = array();

        if (empty($this->recipients)) {
            throw new \UnexpectedValueException('message must have at least one recipient');
        }

        $jsonData['to'] = $this->createTo();
        if ($this->collapseKey) {
            $jsonData['collapse_key'] = $this->collapseKey;
        }
        if ($this->data) {
            $jsonData['data'] = $this->data;
        }
        if ($this->priority) {
            $jsonData['priority'] = $this->priority;
        }
        if ($this->notification) {
            $jsonData['notification'] = $this->notification;
        }

        return $jsonData;
    }

    private function createTo()
    {
        switch ($this->recipientType) {
            case Topic::class:
                return implode(
                ' || ',
                array_map(function (Topic $topic) { return sprintf("'%s' in topics", $topic->getName()); }, $this->recipients)
                );
                break;
            default:
                throw new \UnexpectedValueException('currently phpFCM only supports topic messages');
                break;
        }
    }
}