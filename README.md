# PSWinCom SMS gateway

This library implements PSWinCom's SMS gateway using XML over HTTP(s). For now, only the CPA and bulk SMS messages are supported.

## Installation

```
composer require dc/sms_pswincom
```

or in `composer.json`

```json
"require": {
    "dc/sms_pswincom": "master"
}
```

## Sending messages

```php
require_once "vendor/autoload.php";

$configuration = new \DC\SMS\PSWinCom\Configuration();
$configuration->username = "username";
$configuration->password = "password";
$gateway = new \DC\SMS\PSWinCom\Gateway($configuration);

$message = new \DC\SMS\TextMessage("message text", "<msisdn>");

$receipt = $gateway->sendMessage($message);

if ($receipt->wasEnqueued()) {
    echo "Enqueued";
} else {
    echo "Failed";
}
```

## Processing delivery reports

Set up your delivery end point so you can receive the XML data PSWinCom sends you. Here is an example:

```php
// assuming you have $gateway from above example
$report = $gateway->parseDeliveryReport(file_get_contents('php://input'));

// by default, use the successful response for the gateway
$return = $report->getSuccessfullyProcessedResponse();
if ($report->isSuccess()) {
  if (!$db->markMessageReceived($report->getMessageIdentifier())) {
    // we somehow couldn't save the information, so notify the gateway
    $return = $report->getErrorInProcessingReport();
  }
}

// tell the gateway that we processed (or didn't process) the message
http_response_code($return->getHTTPResponseCode());
foreach ($return->getHeaders() as $key => $value) {
	header(sprintf("%s: %s", $key, $value));
}
echo $return->getContent();
```