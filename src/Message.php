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
    /** @var Recipient[] */
    private $recipients = array();
    private $recipientType;
    private $timeToLive;
    private $delayWhileIdle;

    /**
     * Represents the app's "Send-to-Sync" message.
     *
     * @var bool
     */
    private $contentAvailableFlag;

    /**
     * This is just a convenient and generic array for setting additional parameters,
     * instead of adding a new property for each new parameter.
     * 
     * @example Example: you can set specific info like android_channel_id or sound. See more at: https://firebase.google.com/docs/cloud-messaging/http-server-ref
     * Usage: $message->setAdditionalParameters('android_channel_id', 'YOUR_CHANNEL_ID');
     * 
     * @var array
     */
    private $additionalParameters = [];

    /**
     * where should the message go
     *
     * @param Recipient $recipient
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
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

    /**
     * @see https://firebase.google.com/docs/cloud-messaging/concept-options#ttl
     *
     * @param integer $ttl
     *
     * @return \paragraph1\phpFCM\Message
     */
    public function setTimeToLive($ttl)
    {
        $this->timeToLive = $ttl;

        return $this;
    }

    /**
     * @see https://firebase.google.com/docs/cloud-messaging/concept-options#lifetime
     *
     * @param bool $delayWhileIdle
     *
     * @return \paragraph1\phpFCM\Message
     */
    public function setDelayWhileIdle($delayWhileIdle)
    {
        $this->delayWhileIdle = $delayWhileIdle;
        return $this;
    }

    /**
     * @see https://firebase.google.com/docs/cloud-messaging/concept-options#collapsible_and_non-collapsible_messages
     *
     * @return \paragraph1\phpFCM\Message
     */
    public function setContentAvailable() {
        $this->contentAvailableFlag = TRUE;
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
        if ($this->timeToLive) {
            $jsonData['time_to_live'] = (int)$this->timeToLive;
        }
        if ($this->delayWhileIdle) {
            $jsonData['delay_while_idle'] = (bool)$this->delayWhileIdle;
        }
        if ($this->contentAvailableFlag === TRUE) {
            $jsonData['content_available'] = TRUE;
        }

        return $jsonData;
    }

    private function createTo(array &$jsonData)
    {
        switch ($this->recipientType) {
            case Topic::class:
                if (count($this->recipients) > 1 || is_array(current($this->recipients)->getIdentifier())) {
                    $topics = array_map(
                        function (Topic $topic) {
                            $identity = $topic->getIdentifier();

                            if (is_array($identity)) {
                                $conditions = array_map(function ($condition) {
                                    return sprintf("'%s' in topics", $condition);
                                }, $identity);

                                return '(' . implode(' && ', $conditions) . ')';
                            } else {
                                return sprintf("'%s' in topics", $topic->getIdentifier());
                            }
                        },
                        $this->recipients
                    );
                    $jsonData['condition'] = implode(' || ', $topics);
                    return;
                }
                $jsonData['to'] = sprintf('/topics/%s', current($this->recipients)->getIdentifier());
                break;
            default:
                if (count($this->recipients) === 1) {
                    $jsonData['to'] = current($this->recipients)->getIdentifier();
                } elseif(count($this->recipients) > 1) {
                    $jsonData['registration_ids'] = array();

                    foreach($this->recipients as $recipient) {
                        $jsonData['registration_ids'][] = $recipient->getIdentifier();
                    }
                }
        }
    }

    /**
     * A convenient way for setting additional parameters in a generic array,
     * instead of adding a new property for each new parameter.
     *
     * @example Example: you can set specific info like android_channel_id or sound. See more at: https://firebase.google.com/docs/cloud-messaging/http-server-ref
     * Usage: $message->setAdditionalParameters('android_channel_id', 'YOUR_CHANNEL_ID');
     *
     *
     * @param $key
     * @param $value
     * @return Message
     */
    public function setAdditionalParameters($key, $value)
    {
        $this->additionalParameters[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalParameters()
    {
        return $this->additionalParameters;
    }

}
