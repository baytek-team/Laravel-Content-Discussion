<?php

namespace Baytek\Laravel\Content\Types\Discussion\Controllers\Api;

use Baytek\Laravel\Content\Types\Discussion\Models\Discussion;
use Baytek\Laravel\Content\Types\Discussion\Models\Topic;

use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedTopicScope;
use Baytek\Laravel\Content\Types\Discussion\Scopes\TopicScope;
use Baytek\Laravel\Content\Models\Scopes\TranslationScope;

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

    /**
     * Get all discussions belonging to a specific topic (without children)
     *
     * @param  string  $topic    The key of the topic
     * @param  string  $options  Key/value pairs for sorting, offset, etc
     */
    public function discussions($topic, $options = null)
    {
        //Get the topic
        $topic = Topic::where('contents.key', $topic)->first();

        $discussions = $topic->discussions()
            ->latest()
            ->approved()
            ->options($options)
            ->withContents()
            ->withMeta()
            ->withRelationships()
            ->paginate(5);

        return $discussions->count() ? $discussions : abort(404);
    }
}
