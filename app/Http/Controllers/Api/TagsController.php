<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * Returns a list of tags belonging to the current user.
     *
     * @return array
     */
    public function index()
    {
        return $this->currentUser->tags;
    }

    /**
     * Creates and returns a tag.
     *
     * @param Request $request
     * @return Tag
     */
    public function store(Request $request)
    {
        $tag = new Tag($request->all());
        $this->currentUser->tags()->save($tag);

        return $tag;
    }

    /**
     * Deletes a tag.
     * Note: this does not untag any articles, it just removes the tag from showing as an option.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        $tag = $this->currentUser->tags()->findOrFail($id);
        $tag->delete();
    }
}