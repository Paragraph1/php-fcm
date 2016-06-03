# phpFCM
[![Build Status](https://travis-ci.org/Paragraph1/php-fcm.svg?branch=master)](https://travis-ci.org/Paragraph1/php-fcm)
[![Coverage Status](https://coveralls.io/repos/github/Paragraph1/php-fcm/badge.svg?branch=master)](https://coveralls.io/github/Paragraph1/php-fcm?branch=master)

PHP application server implementation for Firebase Cloud Messaging.
- supports device and topic messages
- currently this app server library only supports sending Messages/Notifications via HTTP.
- thanks to guzzle our library answers in PSR7 compatible response objects
- see the full docs on firebase cloud messaging here : https://firebase.google.com/docs/cloud-messaging/
- Firebase Cloud Messaging HTTP Protocol: https://firebase.google.com/docs/cloud-messaging/http-server-ref#send-downstream for in-depth description


#Setup
The recommended way of installing is using Composer. 
Currently we don't have a stable version, so you must use dev-master. Remember that anything can change:

command line
```
composer require paragraph1/php-fcm
```

composer.json
```
"require": {
    "paragraph1/php-fcm": "dev-master"
}
```

#Send to Device
also see https://firebase.google.com/docs/cloud-messaging/downstream
```php
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;

require_once 'vendor/autoload.php';

$apiKey = 'YOUR SERVER KEY';
$client = new Client();
$client->setApiKey($apiKey);
$client->injectHttpClient(new \GuzzleHttp\Client());

$note = new Notification('test title', 'testing body');
$note->setIcon('notification_icon_resource_name')
    ->setColor('#ffffff')
    ->setBadge(1);

$message = new Message();
$message->addRecipient(new Device('your-device-token'));
$message->setNotification($note)
    ->setData(array('someId' => 111));

$response = $client->send($message);
var_dump($response->getStatusCode());
```

#Send to topic
also see https://firebase.google.com/docs/cloud-messaging/topic-messaging
```php
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Topic;
use paragraph1\phpFCM\Notification;

require_once 'vendor/autoload.php';


$apiKey = 'YOUR SERVER KEY';
$client = new Client();
$client->setApiKey($apiKey);
$client->injectHttpClient(new \GuzzleHttp\Client());

$message = new Message();
$message->addRecipient(new Topic('your-topic'));
$message->setNotification(new Notification('test title', 'testing body'))
    ->setData(array('someId' => 111));

$response = $client->send($message);
var_dump($response->getStatusCode());
```