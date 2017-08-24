<?php

namespace Baytek\Laravel\Content\Types\Discussion\Models;

use Baytek\Laravel\Users\User;
use Baytek\Laravel\Content\Types\Discussion\Scopes\DiscussionScope;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedDiscussionScope;

use Baytek\Laravel\Content\Models\Content;
// use Baytek\Laravel\Content\Types\Discussion\Scopes\TopicScope;

class Discussion extends Content
{

    /**
    * Meta keys that the content expects to save
    * @var Array
    */
    // protected $meta = [
    //  'author_id'
    // ];

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
        static::addGlobalScope(new ApprovedDiscussionScope);
    }

    public function getRouteKeyName()
    {
        return 'contents.id';
    }

    public function setAuthorIdMetadata($id)
    {
        return User::find($id);
    }

    /**
     * Get Discussion Topic
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getTopicAttribute($query)
    {
        return (new Topic)
            ->find(
                $this->relations()
                ->where('relation_type_id', Content::getContentIdByKey('parent-id'))
                ->get()
                ->first()
                ->relation_id
            );
    }

    // /**
    //  * Get Discussion Topic
    //  *
    //  * @param \Illuminate\Database\Eloquent\Builder $query
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */

    // public function getAuthorAttribute($query)
    // {
    //     return (new User)->find($this->getMeta('author_id')));
    // }

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

    // /**
    //  * Populate category slug metadata.
    //  *
    //  * @return String
    //  */
    // public function setCategorySlugMetadata()
    // {
    //     $slug = [];

    //     $parents = array_reverse($this->getParents());
    //     array_shift($parents);

    //     collect($parents)->each(function ($item) use (&$slug) {
    //         if($item->key == 'resource-category') {
    //             return false;
    //         }

    //         array_push($slug, $item->key);
    //     });

    //     return implode('/', array_reverse($slug));
    // }

    /**
     * User favourite contents
     */
    public function membersWhoFavourited()
    {
        return $this->belongsToMany(User::class, 'content_user', 'content_id', 'user_id');
    }

}
