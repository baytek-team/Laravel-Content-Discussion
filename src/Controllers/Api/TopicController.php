<?php

namespace Baytek\Laravel\Content\Types\Discussion\Controllers\Api;

use Baytek\Laravel\Content\Types\Discussion\Models\Topic;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedTopicScope;
use Baytek\Laravel\Content\Types\Discussion\Scopes\TopicScope;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        return Topic::with(Topic::$eager)
            ->orderBy('title', 'asc')
            ->get()
            ->load('relations', 'relations.relation', 'relations.relationType', 'meta');
    }


    public function all()
    {
        return Topic::with(Topic::$eager)
            ->orderBy('title', 'asc')
            ->get()
            ->load('relations', 'relations.relation', 'relations.relationType', 'meta');
    }


    // public function discussions($options = null)
    // {
    //     $discussions = Topic::ofType('topic')
    //         ->options($options)
    //         ->withContents()
    //         ->get();

    //     return $topics->count() ? $topics: abort(404);
    // }

    public function get($topic)
    {
        return Topic::find($topic)->load('meta');
    }
}
