<?php

namespace Baytek\Laravel\Content\Types\Discussion\Controllers\Api;

use Baytek\Laravel\Content\Types\Discussion\Models\Discussion;
use Baytek\Laravel\Content\Types\Discussion\Models\Topic;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedDiscussionScope;
use Baytek\Laravel\Content\Controllers\ContentController;
use Baytek\Laravel\Content\Models\Scopes\TranslationScope;
// use App\Events\DiscussionCreated;
// use App\Events\DiscussionShared;
use Baytek\Laravel\Content\Types\Discussion\Requests\DiscussionRequest;
use Baytek\Laravel\Content\Types\Discussion\Requests\ResponseRequest;

use Baytek\Laravel\Users\User;
// use App\Jobs\SendQueuedDiscussionEmail;
// use App\Roles\Member;

use Baytek\Laravel\Content\Controllers\ApiController;
use Illuminate\Http\Request;
use Auth;

class DiscussionController extends ApiController
{
    public function index()
    {
        return Discussion::all()->load('meta');
    }

    public function latest()
    {
        return Discussion::latest()->get()->load('meta');
    }

    public function oldest()
    {
        return Discussion::oldest()->get()->load('meta');
    }

    public function all()
    {
        return Discussion::withoutGlobalScopes([ApprovedDiscussionScope::class, TranslationScope::class])->get()->load('meta');
    }

    /**
     * Create a new discussion
     */
    public function create(DiscussionRequest $request)
    {
        $request->merge(['content' => nl2br(trim($request->get('content')))]);
        $request->merge(['key' => str_slug($request->title)]);
        $content = app(ContentController::class);

        $content->redirects = false;
        $content->setModel(Discussion::class);

        $discussion = $content->contentStore($request);
        $discussion->saveRelation('parent-id', $request->parent_id);
        //$discussion->saveMetadata('author_id', Auth::id());
        $discussion->saveMetadata('response_count', 0);
        $discussion->saveMetadata('notify_users', (isset($request->notifyUsersField) && $request->notifyUsersField) ? true : false);

        //Approve the discussion
        $discussion->onBit(Discussion::APPROVED)->update();
        $discussion->children = [];

        //Broadcast event for admin email
        // event(new DiscussionCreated($discussion));

        //Temporary fix for cache issue
        event(new \Baytek\Laravel\Content\Events\ContentEvent($discussion));

        //Check if the members need to be notified
        // if (isset($request->notifyUsersField) && $request->notifyUsersField) {
        //     $users = User::role(Member::ROLE)->get();

        //     //Dispatch job for delayed member email
        //     $users->each(function ($user) use ($discussion) {
        //         //Delay is in seconds, 3600 = 1 hour
        //         $job = (new SendQueuedDiscussionEmail($user, $discussion))->delay(3600);
        //         $this->dispatch($job);
        //     });
        // }

        return response()->json([
            'status' => 'success',
            'discussion' => $discussion->load('relations', 'relations.relation', 'relations.relationType', 'meta'),
            'message' => ___('Discussion created successfully.'),
        ]);
    }

    /**
     * Edit an existing discussion
     */
    public function save(DiscussionRequest $request, Discussion $discussion)
    {
        if ( Auth::user()->id == $discussion->metadata('author_id')->id ) {

            //Do the save
            $request->merge(['content' => nl2br(trim($request->get('content')))]);
            $discussion->update($request->all());

            //If there is a parent_id, check if it's different and if yes, updated it
            if ($request->parent_id) {
                $parent_id = $discussion->getRelationship('parent-id');

                if ($parent_id && $parent_id->id != $request->parent_id) {
                    $discussion->removeRelationByType('parent-id');
                    $discussion->saveRelation('parent-id', $request->parent_id);
                }
            }

            $message = ___('Discussion successfully updated.');
            $status = 'success';
        }
        else {
            $message = ___('You are not authorized to update this.');
            $status = 'error';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'type' => 'discussionUpdated',
            'discussion' => $discussion->load('relations', 'relations.relation', 'relations.relationType', 'meta')
        ]);
    }

    /**
     * Reply to an existing discussion
     */
    public function reply(ResponseRequest $request, Discussion $discussion)
    {
        //Create a title and key, based on the parent
        $request->merge(['title' => 'RE: '.$discussion->id.' '.time().'-'.uniqid()]);
        $request->merge(['key' => str_slug($request->title)]);
        $request->merge(['content' => nl2br(trim($request->get('content')))]);

        $content = app(ContentController::class);

        $content->redirects = false;
        $content->setModel(Discussion::class);

        $response = $content->contentStore($request);
        $response->saveRelation('parent-id', $discussion->id);

        //Approve response automatically
        $response->onBit(Discussion::APPROVED)->update();
        $response->children = [];

        //Increment the ancestor Discussion (either parent or grandparent) response count
        $ancestor = content($request->ancestor_id, true, Discussion::class)->load('meta');
        $ancestor->saveMetadata('response_count', (int)$ancestor->getMeta('response_count') + 1);

        //Send the response
        $message = ___('Discussion successfully created.');
        $status = 'success';

        return response()->json([
            'status' => $status,
            'message' => $message,
            'type' => 'discussionCreated',
            'discussion' => $response->load('relations', 'relations.relation', 'relations.relationType', 'meta')
        ]);
    }

    /**
     * Delete a discussion
     */
    public function delete(Discussion $discussion)
    {
        if ( Auth::user()->id == $discussion->metadata('author_id')->id ) {

            //Do the delete
            $discussion->offBit(Discussion::APPROVED)->update();
            $discussion->onBit(Discussion::DELETED)->update();

            $message = ___('Discussion successfully deleted.');
            $status = 'success';
        }
        else {
            $message = ___('You are not authorized to delete this.');
            $status = 'error';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'type' => 'discussionDeleted',
            'discussion' => $discussion
        ]);
    }

    /**
     * Get a Discussion
     * @param  String $topic Category key
     * @param  String $parameters  Discussion key
     * @return String           Returns the discussion values as JSON
     */
    public function get($topic, $discussion)
    {
        $discussion = $this->discussion($topic, $discussion)->first();
        $discussion->children = Discussion::childrenOf($discussion->key)
            ->withContents()
            ->withoutGlobalScopes([ApprovedDiscussionScope::class, TranslationScope::class])
            ->get();

        //Get all children of children
        foreach ($discussion->children as $index => $child) {
            $discussion->children[$index]->children = Discussion::childrenOf($child->key)
                ->withContents()
                ->withoutGlobalScopes([ApprovedDiscussionScope::class, TranslationScope::class])
                ->get();
        }

        return $discussion->count() ? $discussion: abort(404);
    }

    /**
     * Get all discussions belonging to a specific topic (without children)
     */
    public function topicDiscussions($topic, $options = null)
    {
        //Get the topic
        $topic = Topic::where('key', $topic)->first();

        $discussions = Discussion::childrenOf($topic->id)
            ->withoutGlobalScope(TranslationScope::class)
            ->withStatus('r', Discussion::APPROVED)
            ->options($options)
            ->withContents()
            ->paginate(5);

        return $discussions->count() ? $discussions: abort(404);
    }

    /**
     * Get a Discussion (without children)
     * @param  String $topic Category key
     * @param  String $parameters  Discussion key
     * @return String           Returns the discussion values as JSON
     */
    public function discussion($topic, $discussion)
    {
        $discussion = Discussion::childOfType((new Discussion)->getWithPath('discussion-topic/' . $topic), 'discussion', $discussion)
            ->withoutGlobalScope(TranslationScope::class)
            ->withContents()
            ->get();

        return $discussion->count() ? $discussion: abort(404);
    }

    /**
     * Marks a discussion as favourite
     * @param  String $topic Category key
     * @param  String $discussion Discussion key
     * @return String           Returns the saved discussion values as JSON
     */
    public function favourite(Request $request, $topic, $discussion)
    {
        //Get the discussion
        $discussion = $this->discussion($topic, $discussion)->first();

        $members = $discussion->membersWhoFavourited();
        //Get the content_user relationship
        $favourite = $members->find($request->member);

        //If there is a result, delete it
        if ($favourite->isNotEmpty()) {
            $members->detach($request->member['id']);
            $favourited = false;

        } //If there is no result, create it
        else {
            $members->attach($request->member['id']);
            $favourited = true;
        }

        return response()->json([
            'status' => 'success',
            'favourite' => $favourited,
        ]);
    }

    /**
     * Shares a discussion
     * @param  String $topic Category key
     * @param  String $discussion Discussion key
     * @return String           Returns the saved discussion values as JSON
     */
    public function share(Request $request, $topic, $discussion)
    {
        // Trigger the share event to send an email to the user
        // event(new DiscussionShared($request, $this->discussion($topic, $discussion)->first(), $topic));

        return response()->json([
            'status' => 'success',
            'message' => ___('Discussion shared!')
        ]);
    }

    /**
     * Search top-level discussions title & content for a query
     */
    public function search(Request $request, $options = null)
    {
        $query = $request->query->get('query') ?: '';

        $discussions = Discussion::childrenOfType(Topic::all(), 'discussion')
            ->distinct()
            ->where(function($q) use ($query) {
                $q->where('r.title', 'like', $query.'%')
                ->orWhere('r.title', 'like', '%'.$query)
                ->orWhere('r.title', 'like', '%'.$query.'%')
                ->orWhere('r.content', 'like', $query.'%')
                ->orWhere('r.content', 'like', '%'.$query)
                ->orWhere('r.content', 'like', '%'.$query.'%');
            })
            ->options($options)
            ->withStatus('r', Discussion::APPROVED)
            ->paginate(5);

        return $discussions->count() ? $discussions: abort(404);
    }

    /**
     * Return all top-level discussions with sorting options
     */
    public function top(Request $request, $options = null)
    {
        $discussions = Discussion::childrenOfType(Topic::all(), 'discussion')
            ->options($options)
            ->withStatus('r', Discussion::APPROVED)
            ->paginate(5);

        return $discussions->count() ? $discussions: abort(404);
    }

    /**
     * Return three top-level discussions
     */
    public function dashboard()
    {
        $discussions = Discussion::childrenOfType(Topic::all(), 'discussion')
            ->withContents()
            ->withStatus('r', Discussion::APPROVED)
            ->latest('r.created_at')
            ->paginate(3);

        return $discussions->count() ? $discussions: abort(404);
    }

    /**
     * Get a member's discussions
     */
    public function byMember($member, $options = null)
    {
        $discussions = Discussion::childenOfTypeWhereMetadata(Topic::all(), 'discussion', 'author_id', $member)
            ->options($options)
            ->withContents()
            ->withStatus('r', Discussion::APPROVED)
            ->paginate(5);

        return $discussions;
    }
}
