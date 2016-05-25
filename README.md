# phpFCM
PHP Firebase Messaging

#Example
```
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Topic;
use paragraph1\phpFCM\Notification;

require_once 'vendor/autoload.php';


$apiKey = 'YOUR SERVER KEY';
$client = new Client();
$client->setApiKey($apiKey);
$client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

$message = new Message();
$message->addRecipient(new Topic('news'));
$message->setNotification(new Notification('test title', 'testing body'))
    ->setData(array('someId' => 111));

$response = $client->send($message);

var_dump($response->statusCode, $response->reasonPhrase);
```