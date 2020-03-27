# audience-network

[简体中文](README.md) | [ENGLISH](README_EN.md)

![PHP Composer](https://github.com/xuanskyer/audience-network/workflows/PHP%20Composer/badge.svg?branch=master)

Get Facebook Audience Network ads report.

## Usage

```

require_once   './vendor/autoload.php';
use AudienceNetwork\AudienceNetwork;

AudienceNetwork::init([
    'access_token' => 'your access token',
    'property_id' => 'your property id'
]);

$params = [
    'metric' => '',
    'breakdowns' => ['placement', 'platform'],
    'since' => date('Y-m-d'),
    'ordering_column' => 'time',
    'ordering_type' => 'descending',
    'aggregation_period' => 'day',

];
$body = AudienceNetwork::async($params);
var_dump($body);
var_dump(AudienceNetwork::$ret);
/**
* waiting for  facebook result completed
*/
$res = AudienceNetwork::getAsyncResult();
var_dump(AudienceNetwork::$ret);
var_dump($res);
```

OR 


```

require_once   './vendor/autoload.php';
use AudienceNetwork\AudienceNetwork;

$params = [
    'access_token' => 'your access token',
    'property_id' => 'your property id'
    'metric' => '',
    'breakdowns' => ['placement', 'platform'],
    'since' => date('Y-m-d'),
    'ordering_column' => 'time',
    'ordering_type' => 'descending',
    'aggregation_period' => 'day',

];
$body = AudienceNetwork::async($params);
var_dump($body);
var_dump(AudienceNetwork::$ret);
/**
* waiting for  facebook result completed
*/
$res = AudienceNetwork::getAsyncResult();
var_dump(AudienceNetwork::$ret);
var_dump($res);
```

## BTW

If you want to get breakdown data , by `Ad Formats`

can set breakdown `display_format` like this:

```
   'breakdowns' => ['placement', 'platform', 'display_format'],
```

There is no explanation in the document, but it can be used in this way
