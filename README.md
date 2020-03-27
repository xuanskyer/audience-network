# audience-network

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
$res = AudienceNetwork::getAsyncResult();
var_dump(AudienceNetwork::$ret);
var_dump($res);
```