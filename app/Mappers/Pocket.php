<?php

namespace App\Mappers;

class Pocket
{
    /**
     * @param string $response A string like 'code=abcdefg'
     * @return string The request token
     */
    public function mapRequestTokenResponse($response)
    {
        $code = explode('=', $response);

        return $code[1];
    }

    /**
     * @param string $response A string like: 'access_token=063cb415-02b3-8b53-4260-8f6191&username=matei.carpen%40domain.com'
     * @return array|bool
     */
    public function mapAccessTokenResponse($response)
    {
        $response = explode('&', $response);

        if ($response[0] == '' || $response[1] == '') {
            return false;
        }

        $accessToken = explode('=', $response[0])[1];
        $email = urldecode(explode('=', $response[1])[1]);

        return [
            'pocketAccessToken' => $accessToken,
            'email' => $email
        ];
    }

    /**
     * @param $response
     * @return array
     */
    public function mapArticleListResponse($response)
    {
        $articles = json_decode($response, true);
        $articles = array_values($articles['list']);

        $keep = [
            'item_id'        => 'id',
            'resolved_url'   => 'url',
            'resolved_title' => 'title',
            'excerpt'        => 'excerpt',
            'time_added'     => 'timestamp',
            'image'          => 'image'
        ];

        //only keep the attributes that we need, and format the data to a readable format
        foreach ($articles as &$article) {
            foreach ($article as $key => $value) {
                unset($article[$key]);

                if (empty($keep[$key])) {
                    continue;
                }

                $newKey = $keep[$key];
                switch ($key) {
                    case 'time_added':
                        $article[$newKey] = $value;
                        $article['created_on'] = date('d-m-Y', $value);
                        break;

                    case 'image':
                        $article[$newKey] = $value['src'];
                        break;

                    default:
                        $article[$newKey] = $value;
                }
            }
        }

        return $articles;
    }
}