# audience-network

[简体中文](README.md) | [ENGLISH](README_EN.md)

![PHP Composer](https://github.com/xuanskyer/audience-network/workflows/PHP%20Composer/badge.svg?branch=master)

获取 Facebook 的 Audience Network 广告报告

## 用法

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
* 等待Facebook的异步接口处理完成结果后，查询
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

如果你想通过 `Ad Formats` 来查询细分数据，可以设置 breakdown 参数`display_format`：

```
   'breakdowns' => ['placement', 'platform', 'display_format'],
```

Facebook的官方文档中并没有说明这种用法，不过亲测确实可以这么用。
