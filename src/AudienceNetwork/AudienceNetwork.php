<?php

namespace AudienceNetwork;

class AudienceNetwork
{
    //return code
    const RET_SUCCESS               = [0, 'success!'];
    const RET_ACCESS_TOKEN_ERR      = [-1, "access token need!"];
    const RET_PROPERTY_ID_ERR       = [-2, "property id need!"];
    const RET_SYNC_URL_ERR          = [-3, "sync url need!"];
    const RET_ASYNC_URL_ERR         = [-4, "async url need!"];
    const RET_SYNC_ERR              = [-5, "sync request err"];
    const RET_ASYNC_ERR             = [-6, "async request err"];
    const RET_ASYNC_RESULT_LINK_ERR = [-7, "async result link err"];

    const URL_SYNC_FORMAT  = "https://graph.facebook.com/v6.0/%s/adnetworkanalytics/?access_token=%s";
    const URL_ASYNC_FORMAT = "https://graph.facebook.com/v6.0/%s/adnetworkanalytics/?access_token=%s";

    const ROWS_FIELD = [
        'property'                     => null,
        'day'                          => null,
        'country'                      => null,
        'platform'                     => null,
        'display_format'               => null,
        'placement_name'               => null,
        'placement'                    => null,
        'fb_ad_network_request'        => null,
        'fb_ad_network_filled_request' => null,
        'fb_ad_network_fill_rate'      => null,
        'fb_ad_network_imp'            => null,
        'fb_ad_network_show_rate'      => null,
        'fb_ad_network_click'          => null,
        'fb_ad_network_ctr'            => null,
        'fb_ad_network_cpm'            => null,
        'fb_ad_network_revenue'        => null,
    ];

    protected static $client       = null;
    protected static $property_id  = null;
    protected static $access_token = null;
    protected static $sync_url     = null;
    protected static $async_url    = null;
    protected static $async_ret    = [
        'query_id'          => null,
        'async_result_link' => null,
    ];
    protected static $ret          = [];

    protected static function buildSyncUrl()
    {
        self::$sync_url = sprintf(self::URL_SYNC_FORMAT, self::$property_id, self::$access_token);
    }

    protected static function buildAsyncUrl()
    {
        self::$async_url = sprintf(self::URL_ASYNC_FORMAT, self::$property_id, self::$access_token);
    }

    /**
     * @param array $params
     * [
     *      'access_token' => 'your access token',
     *      'property_id' => 'your property id',
     * ]
     */
    public static function init($params = [])
    {
        if (!array_key_exists('property_id', $params)) {
            self::$ret = self::RET_PROPERTY_ID_ERR;
        } else {
            self::$property_id = $params['property_id'];
        }
        if (!array_key_exists('access_token', $params)) {
            self::$ret = self::RET_ACCESS_TOKEN_ERR;
        } else {
            self::$access_token = $params['access_token'];
        }
        self::buildSyncUrl();
        self::buildAsyncUrl();
        self::$client = new \GuzzleHttp\Client();
    }

    public static function sync()
    {
        $response = self::$client->request('GET', self::$sync_url);
        if (200 != $response->getStatusCode()) {
            self::$ret = self::RET_SYNC_ERR;
        }
        return $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'
    }

    public static function async()
    {
        $response = self::$client->request('POST', self::$async_url);
        if (200 != $response->getStatusCode()) {
            self::$ret = self::RET_ASYNC_ERR;
        }
        $body            = $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'
        self::$async_ret = json_decode($body, true);
    }

    public static function getAsyncResult()
    {
        if (!array_key_exists('async_result_link', self::$async_ret)) {
            self::$ret = self::RET_ASYNC_RESULT_LINK_ERR;
            return null;
        } else {
            $response = self::$client->request('GET', self::$async_ret['async_result_link']);
            if (200 != $response->getStatusCode()) {
                self::$ret = self::RET_SYNC_ERR;
            }
            return $response->getBody(); // '{"id": 1420053, "name": "guzzle", ...}'
        }
    }

}