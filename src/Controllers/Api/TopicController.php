<?php

namespace Baytek\Laravel\Content\Types\Discussion\Controllers\Api;

use Baytek\Laravel\Content\Types\Discussion\Models\Topic;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedTopicScope;
use Baytek\Laravel\Content\Types\Discussion\Scopes\TopicScope;

use Baytek\Laravel\Content\Controllers\ApiController;
use Illuminate\Http\Request;

class TopicController extends ApiController
{
    public function index()
    {
        return Topic::with(Topic::$eager)
            ->withRelationships()
            ->withMeta()
            ->orderBy('title', 'asc')
            ->get();
    }

    public function all()
    {
        return Topic::with(Topic::$eager)
            ->withRelationships()
            ->withMeta()
            ->orderBy('title', 'asc')
            ->get();
    }

    public function get($topic)
    {
        return Topic::find($topic)->load('meta');
    }
}
