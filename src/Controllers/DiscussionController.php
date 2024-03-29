<?php

namespace Baytek\Laravel\Content\Types\Discussion\Controllers;

// use App\Jobs\SendQueuedDiscussionEmail;
use App\Roles\Member;

use Baytek\Laravel\Content\Controllers\ContentController;
use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content\Types\Discussion\Models\Discussion;
use Baytek\Laravel\Content\Types\Discussion\Models\Topic;
use Baytek\Laravel\Content\Types\Discussion\Requests\DiscussionRequest;
use Baytek\Laravel\Content\Types\Discussion\Requests\ResponseRequest;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedDiscussionScope;
use Baytek\Laravel\Users\User;

use Illuminate\Http\Request;

use View;

class DiscussionController extends ContentController
{
    /**
     * The model the Content Controller super class will use to access the resource
     *
     * @var App\ContentTypes\Discussion\Models\Discussion
     */
    protected $model = Discussion::class;

    protected $viewPrefix = 'admin';

    /**
     * List of views this content type uses
     * @var [type]
     */
    protected $views = [
        'index' => 'discussion.index',
        'create' => 'discussion.create',
        'edit' => 'discussion.edit',
        'show' => 'discussion.show',
        'translate' => 'discussion.translate',
    ];

    protected $redirectsKey = 'discussion';

    /**
     * Show the index of all content with content type 'discussion'
     *
     * @return \Illuminate\Http\Response
     */
    public function index($topicID = null)
    {
        $this->viewData['index'] = [
            'discussions' => Discussion::topLevel()
                ->withMeta()
                ->withRelationships()
                ->approved()
                ->latest()
                ->paginate(),
            'filter' => 'active',
        ];

        return parent::contentIndex();
    }

    /**
     * Show the index of all content with content type 'discussion'
     *
     * @return \Illuminate\Http\Response
     */
    public function children(Discussion $discussion)
    {
        return view('discussions::discussion.children', [
            'discussions' => $discussion->children()
                ->approved()
                ->withMeta()
                ->withRelationships()
                ->withContents()
                ->paginate(),
            'parentDiscussion' => $discussion,
            'filter' => 'active',
        ]);
    }

    /**
     * Show the index of all content with content type 'discussion'
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted()
    {
        $this->viewData['index'] = [
            'discussions' => Discussion::topLevel()
                ->withMeta()
                ->withRelationships()
                ->deleted()
                ->latest()
                ->paginate(),
            'filter' => 'deleted',
        ];

        return parent::contentIndex();
    }

    /**
     * Show the form for creating a new discussion.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->viewData['create'] = [
            'parents' => Discussion::where('contents.key', 'discussion')->get(),
            'topics' => Topic::all(),
        ];

        return parent::contentCreate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->redirects = false;

        $request->merge(['key' => str_slug($request->title)]);
        $request->merge(['content' => nl2br(trim($request->get('content')))]);

        $discussion = parent::contentStore($request);
        $discussion->saveRelation('parent-id', $request->parent_id);
        $discussion->saveMetadata('response_count', 0);

        //Check if the members need to be notified
        // if (isset($request->notify) && $request->notify) {
        //     $users = User::role(Member::ROLE)->get();

        //     //Dispatch job for delayed member email
        //     $users->each(function ($user) use ($discussion) {
        //         //Delay is in seconds, 3600 = 1 hour
        //         $job = (new SendQueuedDiscussionEmail($user, $discussion))->delay(3600);
        //         $this->dispatch($job);
        //     });
        // }

        $discussion->offBit(Discussion::DELETED);
        $discussion->onBit(Discussion::APPROVED)->save();

        //Update the server cache
        event(new ContentEvent($discussion));

        return redirect(route('discussion.index', $discussion));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $discussion = $this->bound($id);

        $this->viewData['edit'] = [
            'parents' => Discussion::where('contents.key', 'discussion')->get(),
            'parent_id' => $discussion->parent(),
            'topics' => Topic::all(),
        ];

        return parent::contentEdit($id);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->redirects = false;

        $request->merge([
            'content' => nl2br(trim($request->get('content')))
        ]);

        $discussion = parent::contentUpdate($request, $id);
        $discussion->removeRelationByType('parent-id');
        $discussion->saveRelation('parent-id', $request->parent_id);

        //Update the server cache
        event(new ContentEvent($discussion));

        //Trigger update events for discussion topic
        $topic = Topic::find($request->parent_id);
        $topic->touch();
        event(new ContentEvent($topic));

        return redirect(route($this->names['singular'].'.edit', $discussion));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function editResponse($id)
    {
        $discussion = $this->bound($id);

        return view('discussions::response.edit', [
            'discussion' => $discussion,
        ]);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateResponse(ResponseRequest $request, $id)
    {
        $this->redirects = false;

        $request->merge(['content' => nl2br(trim($request->get('content')))]);
        $discussion = parent::contentUpdate($request, $id);

        return redirect(route($this->names['singular'].'.editResponse', $discussion));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        return parent::contentDestroy($request, $id);
    }

    /**
     * Set the status of the discussion to approved
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, Discussion $discussion)
    {
        $discussion->offBit(Discussion::DELETED);
        $discussion->onBit(Discussion::APPROVED)->update();

        return redirect()->back();
    }

    /**
     * Set the status of the discussion to deleted/declined
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function decline(Request $request, Discussion $discussion)
    {
        $discussion->offBit(Discussion::APPROVED);
        $discussion->onBit(Discussion::DELETED)->update();

        return redirect()->back();
    }
}