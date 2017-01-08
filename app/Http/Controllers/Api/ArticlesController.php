<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Pocket;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    /** @var Pocket */
    private $pocketClient;

    public function __construct(AuthManager $authManager, Pocket $pocketClient)
    {
        parent::__construct($authManager);

        $this->pocketClient = $pocketClient;
    }

    /**
     * Returns a list of articles belonging to the current user.
     * Only articles with a create time after last_article_time are returned.
     *
     * @return array
     */
    public function index()
    {
        $accessToken = $this->currentUser->pocket_access_token;
        $lastArticleTime = $this->currentUser->last_article_time;

        return $this->pocketClient->getList($accessToken, $lastArticleTime);
    }

    /**
     * Marks the article as read.
     * It does that by moving the last_article_time flag after the create time of the article
     *
     * @param Request $request
     */
    public function pass(Request $request)
    {
        $id = $request->input('id'); // just here for logging for now
        $time = $request->input('timestamp');

        $this->currentUser->saveLastArticleTime($time);
    }

    /**
     * Marks the article as archived (in Pocket) and moves the last_article_time after the create time of the article
     *
     * @param Request $request
     */
    public function archive(Request $request)
    {
        $id = $request->input('id');
        $timestamp = $request->input('timestamp');

        $accessToken = $this->currentUser->pocket_access_token;

        $this->pocketClient->archiveItem($accessToken, $id);
        $this->currentUser->saveLastArticleTime($timestamp);
    }

    /**
     * Tags the article with the specified tag.
     *
     * @param Request $request
     */
    public function tag(Request $request)
    {
        $id        = $request->input('id');
        $tagId     = $request->input('tag_id');
        $timestamp = $request->input('timestamp');

        $accessToken = $this->currentUser->pocket_access_token;

        $tag = $this->currentUser->tags()->findOrFail($tagId);

        $this->pocketClient->addTag($accessToken, $id, $tag->name);
        $this->currentUser->saveLastArticleTime($timestamp);
    }

    /**
     * Resets the last_article_time, so that if the list is called again, it will return start from the beginning.
     */
    public function reset()
    {
        $this->currentUser->saveLastArticleTime(0);
    }
}