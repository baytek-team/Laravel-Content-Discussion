<?php

namespace Baytek\Laravel\Content\Types\Discussion\Models;

use Baytek\Laravel\Users\User;

use Baytek\Laravel\Content\Types\Discussion\Scopes\DiscussionScope;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedDiscussionScope;

use Baytek\Laravel\Content\Types\Discussion\Models\Topic;
use Baytek\Laravel\Content\Models\Content;

class Discussion extends Content
{
    /**
    * Content keys that will be saved to the relation tables
    * @var Array
    */
    public $relationships = [
        'content-type' => 'discussion'
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
        parent::boot();
        static::withoutGlobalScope(\Baytek\Laravel\Content\Models\Scopes\TranslationScope::class);
        static::addGlobalScope(new DiscussionScope);
        //static::addGlobalScope(new ApprovedDiscussionScope);
    }

    public function getRouteKeyName()
    {
        return 'contents.id';
    }

    /**
     * Get the topic of this discussion, if it's a first level discussion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function topic()
    {
        return $this->belongsToOneOrMany(Topic::class, 'content_relations', 'content_id', 'relation_id')
            ->wherePivot('relation_type_id', content('relation-type/parent-id', false))
            ->expectOne();
    }

    /**
     * Get the parent discussion of this discussion, if it's a child level discussion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    // public function parent()
    // {
    //     return $this->belongsToOneOrMany(Discussion::class, 'content_relations', 'content_id', 'relation_id')
    //         ->wherePivot('relation_type_id', content('relation-type/parent-id', false))
    //         ->expectOne();
    // }

    /**
     * Get the responses to this discussion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function children()
    {
        return $this->belongsToOneOrMany(Discussion::class, 'content_relations', 'relation_id', 'content_id')
            ->wherePivot('relation_type_id', content('relation-type/parent-id', false));
    }

    /**
     * Get the user who created this discussion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsToOneOrMany(User::class, 'content_meta', 'content_id', 'value')
            ->wherePivot('key', 'author_id')
            ->expectOne();
    }

    /**
     * Get all the members who have favourited this discussion
     */
    public function membersWhoFavourited()
    {
        return $this->belongsToMany(User::class, 'content_user', 'content_id', 'user_id');
    }

    public function setAuthorIdMetadata($id)
    {
        return User::find($id);
    }

    public function scopeOptions($query, $parameters = null)
    {
        if(!is_null($parameters)) {

            $parameters = (explode('/', $parameters));
            if(count($parameters) % 2 !== 0) {
                throw new \Exception('Illegal number of arguments passed');
            }

            $options = [];
            while (count($parameters)) {
                list($key,$value) = array_splice($parameters, 0, 2);
                $options[$key] = $value;
            }

            if(isset($options['sort'])) {
                switch($options['sort']) {
                    case 'alphabetical':
                        $query->sortAlphabetical();
                        break;
                    case 'newest':
                        $query->sortNewest();
                        break;
                    case 'popular':
                        $query->sortPopular();
                        break;
                }
            }

            if(array_key_exists('limit', $options)) {
                $query->limit($options['limit']);
            }

            if(array_key_exists('offset', $options)) {
                $query->offset($options['offset']);
            }
        }

        return $query;
    }

    /**
     * Scope a query to only include approved resources.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->withStatus(Discussion::APPROVED);
    }

    /**
     * Scope a query to only include deleted resources.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeleted($query)
    {
        return $query->withStatus(Discussion::DELETED);
    }

    /**
     * Scope a query to only include top-level discussions
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTopLevel($query)
    {
        //return $query->has('topic');

        /**
         * The above isn't working, due to a conflict in table selection when adding in content type scope for the topics, so the slightly less elegant solution below is being used instead
         */

        return $query->whereExists(function ($q) {
            $prefix = env('DB_PREFIX');

            $q->select(\DB::raw(1))
                ->from('content_relations')
                ->whereRaw("{$prefix}contents.id = {$prefix}content_relations.content_id")
                ->where('content_relations.relation_type_id', 4)
                ->whereIn('content_relations.relation_id', Topic::select('contents.id')->get()->pluck('id'));
        });
    }
}
