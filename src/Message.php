<?php
namespace paragraph1\phpFCM;

use paragraph1\phpFCM\Recipient\Recipient;
use paragraph1\phpFCM\Recipient\Topic;
use paragraph1\phpFCM\Recipient\Device;

/**
 * @author palbertini
 */
class Message implements \JsonSerializable
{
    const PRIORITY_HIGH = 'high',
        PRIORITY_NORMAL = 'normal';

    private $notification;
    private $collapseKey;

    /**
     * set priority to "high" by default. Otherwise iOS push notifications (apns) will not wake up app
     *
     * @var string
     */
    private $priority = self::PRIORITY_HIGH;
    private $data;
    private $recipients = array();
    private $recipientType;

    /**
     * where should the message go
     *
     * @param Recipient $recipient
     *
     * @return \paragraph1\phpFCM\Message
     */
    public function addRecipient(Recipient $recipient)
    {
        if (!$recipient instanceof Device && !$recipient instanceof Topic) {
            throw new \UnexpectedValueException('currently phpFCM only supports topic and single device messages');
        }

        if (!isset($this->recipientType)) {
            $this->recipientType = get_class($recipient);
        }

        if ($this->recipientType !== get_class($recipient)) {
            throw new \InvalidArgumentException('mixed recepient types are not supported by FCM');
        }
        if ($this->recipientType === Device::class && count($this->recipients) > 0) {
            throw new \InvalidArgumentException('when sending to single devices only one recipient is allowed');
        }

        $this->recipients[] = $recipient;
        return $this;
    }

    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;
        return $this;
    }

    /**
     * @see https://firebase.google.com/docs/cloud-messaging/concept-options#collapsible_and_non-collapsible_messages
     *
     * @param string $collapseKey
     *
     * @return \paragraph1\phpFCM\Message
     */
    public function setCollapseKey($collapseKey)
    {
        $this->collapseKey = $collapseKey;
        return $this;
    }

    /**
     * normal or high, use class constants as value
     * @see https://firebase.google.com/docs/cloud-messaging/concept-options#setting-the-priority-of-a-message
     *
     * @param string $priority use the class constants
     *
     * @return \paragraph1\phpFCM\Message
     */
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

        $this->createTo($jsonData);
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

    private function createTo(array &$jsonData)
    {
        switch ($this->recipientType) {
            case Topic::class:
                if (count($this->recipients) > 1) {
                    $topics = array_map(
                        function (Topic $topic) { return sprintf("'%s' in topics", $topic->getIdentifier()); },
                        $this->recipients
                    );
                    $jsonData['condition'] = implode(' || ', $topics);
                    break;
                }
                $jsonData['to'] = sprintf('/topics/%s', current($this->recipients)->getIdentifier());
                break;
            default:
                if (count($this->recipients) == 1) {
                    $jsonData['to'] = current($this->recipients)->getIdentifier();
                }
        }
    }
}