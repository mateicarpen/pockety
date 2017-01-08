<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use App\Mappers\Pocket as Mapper;

class Pocket
{
    const BASE_URL = 'https://getpocket.com';

    const REQUEST_TOKEN_URL = '/v3/oauth/request';
    const ACCESS_TOKEN_URL  = '/v3/oauth/authorize';
    const LIST_URL          = '/v3/get';
    const ACTION_URL        = '/v3/send';
    const AUTHORIZE_URL     = '/auth/authorize';

    /** @var Client */
    private $client;

    /** @var Mapper */
    private $mapper;

    /** @var string */
    private $consumerKey;

    public function __construct(Client $client, Mapper $mapper, Repository $config)
    {
        $this->client      = $client;
        $this->mapper      = $mapper;
        $this->consumerKey = $config->get('services.pocket')['consumerKey'];
    }

    /**
     * Returns the url the users have to go to in order to authenticate on the Pocket server.
     *
     * @param string $redirectUrl
     * @return string
     */
    public function getConnectUrl($redirectUrl)
    {
        $url = self::BASE_URL . self::AUTHORIZE_URL;
        $requestToken = $this->getRequestToken($redirectUrl);
        $redirectUri = "$redirectUrl?request_token=$requestToken";

        return  $url . "?request_token=$requestToken&redirect_uri=$redirectUri";
    }

    /**
     * Returns a request token from the Pocket server.
     *
     * @param string $redirectUrl
     * @return string
     */
    public function getRequestToken($redirectUrl)
    {
        $response = $this->makeRequest(self::BASE_URL . self::REQUEST_TOKEN_URL, [
            'consumer_key' => $this->consumerKey,
            'redirect_uri' => $redirectUrl
        ]);

        return $this->mapper->mapRequestTokenResponse($response);
    }

    /**
     * @param string $requestToken
     * @return array
     */
    public function getAccessToken($requestToken)
    {
        $response = $this->makeRequest(self::BASE_URL . self::ACCESS_TOKEN_URL, [
            'consumer_key' => $this->consumerKey,
            'code' => $requestToken
        ]);

        return $this->mapper->mapAccessTokenResponse($response);
    }

    /**
     * Returns the full list of active items from the user's pocket account
     *
     * @param string $accessToken
     * @param int $lastArticleTime
     * @return array
     */
    public function getList($accessToken, $lastArticleTime)
    {
        $response = $this->getRawList($accessToken, $lastArticleTime);

        $articles = $this->mapper->mapArticleListResponse($response);

        return [
            'count' => count($articles),
            'list' => $articles
        ];
    }

    /**
     * @param string $accessToken
     * @param int $itemId
     * @return string
     */
    public function archiveItem($accessToken, $itemId)
    {
        return $this->makeRequest(self::BASE_URL . self::ACTION_URL, [
            'consumer_key' => $this->consumerKey,
            'access_token' => $accessToken,
            'actions' => json_encode([
                [
                    'action' => 'archive',
                    'item_id' => $itemId
                ]
            ])
        ]);
    }

    /**
     * @param string $accessToken
     * @param int $itemId
     * @param string $tag
     * @return string
     */
    public function addTag($accessToken, $itemId, $tag)
    {
        return $this->makeRequest(self::BASE_URL . self::ACTION_URL, [
            'consumer_key' => $this->consumerKey,
            'access_token' => $accessToken,
            'actions' => json_encode([
                [
                    'action' => 'tags_add',
                    'item_id' => $itemId,
                    'tags' => $tag
                ]
            ])
        ]);
    }

    /**
     * @param string $accessToken
     * @param int $lastArticleTime
     * @return string
     */
    private function getRawList($accessToken, $lastArticleTime)
    {
        $url = self::BASE_URL . self::LIST_URL;
        $url .= '?state=unread&tag=_untagged_&sort=oldest&detailType=complete';

        if (!empty($lastArticleTime)) {
            $url .= '&since=' . ($lastArticleTime + 1);
        }

        return $this->makeRequest($url, [
            'consumer_key' => $this->consumerKey,
            'access_token' => $accessToken
        ]);
    }

    /**
     * @param string $url
     * @param array $data
     * @return string
     */
    private function makeRequest($url, $data)
    {
        $response = $this->client->request('POST', $url, [
            'form_params' => $data
        ]);

        return $response->getBody()->getContents();
    }
}