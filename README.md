# phpFCM
[![Build Status](https://travis-ci.org/Paragraph1/php-fcm.svg?branch=master)](https://travis-ci.org/Paragraph1/php-fcm)

PHP application server implementation for Firebase Cloud Messaging.
- supports device and topic messages
- currently this app server library only supports sending Messages/Notifications via HTTP.

#Setup
via Composer (currently we don't have a stable version, anything can change):
```
"require": {
    "paragraph1/php-fcm": "dev-master"
}
```

#Send to Device
```php
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;

require_once 'vendor/autoload.php';


$apiKey = 'YOUR SERVER KEY';
$client = new Client();
$client->setApiKey($apiKey);
$client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

$message = new Message();
$message->addRecipient(new Device('your-device-token'));
$message->setNotification(new Notification('test title', 'testing body'))
    ->setData(array('someId' => 111));

$response = $client->send($message);
var_dump($response->getStatusCode());
```

#Send to topic
Currently sending to topics only supports a single topic as recipient. Mutliple topic as outlined
in the google docs don't seem to work, yet.
```php
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Topic;
use paragraph1\phpFCM\Notification;

require_once 'vendor/autoload.php';


$apiKey = 'YOUR SERVER KEY';
$client = new Client();
$client->setApiKey($apiKey);
$client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

$message = new Message();
$message->addRecipient(new Topic('your-topic'));
$message->setNotification(new Notification('test title', 'testing body'))
    ->setData(array('someId' => 111));

$response = $client->send($message);
var_dump($response->getStatusCode());
```