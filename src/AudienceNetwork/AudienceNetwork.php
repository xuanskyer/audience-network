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

    protected static $client       = null;
    protected static $property_id  = null;
    protected static $access_token = null;
    protected static $sync_url     = null;
    protected static $async_url    = null;
    protected static $async_ret    = [
        'query_id'          => null,
        'async_result_link' => null,
    ];
    public static $ret          = self::RET_SUCCESS;

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

    /**
     * @param array $params
     * [
     * 'metric' => 'fb_ad_network_imp',
     * 'breakdowns' => ['country','placement'],
     * 'since' => date('Y-m-d'),
     * 'until' => null,
     * 'filters' => [
     * [
     * 'field' => 'country',
     * 'operator' => 'in',
     * 'values' => ['US', 'JP']
     * ]
     * ],
     * 'ordering_column' => 'time',
     * 'ordering_type' => 'descending',
     * 'aggregation_period' => 'day',
     * ];
     * @return mixed
     */
    public static function sync($params = [])
    {
        try {
            $request_params = self::buildRequestParams($params);
            $response = self::$client->request('GET', self::$sync_url . $request_params);

            if (200 != $response->getStatusCode()) {
                self::$ret = self::RET_SYNC_ERR;
            }
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            self::$ret = [
                $e->getCode(), $e->getMessage()
            ];
            return [];
        }
    }

    public static function async($params = [])
    {
        try{
            $request_params = self::buildRequestParams($params);
            $response = self::$client->request('POST', self::$async_url . $request_params);
            if (200 != $response->getStatusCode()) {
                self::$ret = self::RET_ASYNC_ERR;
            }
            $body            = $response->getBody();
            self::$async_ret = json_decode($body, true);
        }catch (\Exception $e){
            self::$ret = [$e->getCode(), $e->getMessage()];
        }
    }

    public static function getAsyncResult($to_array = true)
    {
        try{
            if (!array_key_exists('async_result_link', self::$async_ret)) {
                self::$ret = self::RET_ASYNC_RESULT_LINK_ERR;
                return null;
            } else {
                $response = self::$client->request('GET', self::$async_ret['async_result_link']);
                if (200 != $response->getStatusCode()) {
                    self::$ret = self::RET_SYNC_ERR;
                }
                return $to_array ? json_decode($response->getBody(), true) : $response->getBody();
            }
        }catch (\Exception $e){
            self::$ret = [$e->getCode(), $e->getMessage()];
            return null;
        }

    }

    protected static function buildRequestParams($params = [])
    {
        $request_params = '';
        if (array_key_exists('metric', $params)) {
            $metric = $params['metric'];
        }
        empty($metric) && $metric = 'fb_ad_network_imp';
        $request_params .= "&metrics=['{$metric}']";

        if (array_key_exists('breakdowns', $params)) {
            $breakdowns_str = json_encode($params['breakdowns'], 1);
            $breakdowns_str && $request_params .= "&breakdowns={$breakdowns_str}";
        }

        $since = date('Y-m-d');
        if (array_key_exists('since', $params)) {
            $since = $params['since'];
        }
        $request_params .= "&since={$since}";
        $until          = null;
        if (array_key_exists('until', $params)) {
            $until = $params['until'];
        }
        $until && $request_params .= "&until={$until}";

        if (array_key_exists('limit', $params)) {
            $request_params .= "&limit={$params['limit']}";
        }
        if (array_key_exists('ordering_column', $params)) {
            $request_params .= "&ordering_column={$params['ordering_column']}";
        }
        if (array_key_exists('ordering_type', $params)) {
            $request_params .= "&ordering_type={$params['ordering_type']}";
        }
        if (array_key_exists('aggregation_period', $params)) {
            $request_params .= "&aggregation_period={$params['aggregation_period']}";
        }
        if (array_key_exists('filters', $params)) {
            $filters_str = self::buildFilters($params['filters']);
            $filters_str && $request_params .= "&filters={$filters_str}";
        }
        return $request_params;
    }

    /**
     * @param array $filters
     * [
     *      [
     *          'field' => 'country',
     *          'operator' => 'in',
     *          'values' => []
     *      ],
     *      [
     *          'field' => 'placement',
     *          'operator' => 'in',
     *          'values' => []
     *      ]
     * ]
     * @return string
     */
    protected static function buildFilters($filters = [])
    {
        return empty($filters) ? '' : json_encode($filters, 1);
    }
}