<?php

namespace Baytek\Laravel\Content\Types\Discussion\Models;

use Baytek\Laravel\Content\Types\Discussion\Models\Discussion;
use Baytek\Laravel\Content\Types\Discussion\Scopes\TopicScope;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedTopicScope;

use Baytek\Laravel\Content\Models\Content;

class Topic extends Content
{

    /**
    * Content keys that will be saved to the relation tables
    * @var Array
    */
    public $relationships = [
        'content-type' => 'discussion-topic'
    ];

    public $translatableMetadata = [
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::addGlobalScope(new TopicScope);
        static::addGlobalScope(new ApprovedTopicScope);
        parent::boot();
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Scope a query to only include approved resources.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->withStatus('contents', Topic::APPROVED);
    }

    /**
     * Scope a query to only include pending resources (require moderation).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->withStatus('contents', ['exclude' => [Topic::APPROVED, Topic::DELETED]]);
    }

    /**
     * Scope a query to only include deleted resources.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeleted($query)
    {
        return $query->withStatus('contents', Topic::DELETED);
    }

    public function setDiscussionCountMetadata()
    {

        return $this->childrenOfType($this->key, 'discussion')->withStatus('r', Discussion::APPROVED)->count();

        // if(Cache::has('category.' . $this->id . '.count')) {
        //  return Cache::get('category.' . $this->id . '.count');
        // }

        // $count = (int)$this->countChildrenOfTypeById($this->id, 'resource')[0]->resource_count;
        // Cache::forever('category.' . $this->id . '.count', $count);

        // return $count;
    }

}
