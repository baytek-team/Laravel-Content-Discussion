<?php

namespace Baytek\Laravel\Content\Types\Discussion\Controllers;

use Baytek\Laravel\Content\Types\Discussion\Models\Topic;
use Baytek\Laravel\Content\Types\Discussion\Requests\TopicRequest;
use Baytek\Laravel\Content\Types\Discussion\Scopes\ApprovedTopicScope;

use Baytek\Laravel\Content\Controllers\ContentController;
use Illuminate\Http\Request;

use View;

class TopicController extends ContentController
{
    /**
     * The model the Content Controller super class will use to access the resource
     *
     * @var App\ContentTypes\Discussion\Models\Discussion
     */
    protected $model = Topic::class;

    protected $viewPrefix = 'admin/discussions';

    /**
     * List of views this content type uses
     * @var [type]
     */
    protected $views = [
        'index' => 'index',
        'create' => 'create',
        'edit' => 'edit',
        'show' => 'show',
    ];

    protected $redirectsKey = 'discussion.topic';

    /**
     * [__construct description]
     *
     * @return  null
     */
    public function __construct()
    {
        $this->loadViewsFrom(resource_path().'/views', 'topic');

        parent::__construct();
    }

    /**
     * Show the index of all content with content type 'discussion'
     *
     * @return \Illuminate\Http\Response
     */
    public function index($topicID = null)
    {
        $this->viewData['index'] = [
            'topics' => Topic::paginate(),
            'filter' => 'active',
        ];

        return parent::contentIndex();
    }

    /**
     * Show the index of all content with content type 'topics'
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted()
    {
        $this->viewData['index'] = [
            'topics' => Topic::withoutGlobalScope(ApprovedTopicScope::class)
                ->deleted()
                ->paginate(),
            'filter' => 'deleted',
        ];

        return parent::contentIndex();
    }

    /**
     * Show the index of all content with content type 'topics'
     *
     * @return \Illuminate\Http\Response
     */
    public function pending()
    {
        $this->viewData['index'] = [
            'topics' => Topic::withoutGlobalScope(ApprovedTopicScope::class)
                ->pending()
                ->paginate(),
            'filter' => 'pending',
        ];

        return parent::contentIndex();
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->viewData['create'] = [
            'parents' => Topic::where('contents.key', 'discussion-topic')->get(),
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

        $topic = parent::contentStore($request);
        $topic->saveRelation('parent-id', $topic->getContentIdByKey('discussion-topic'));

        return redirect(route('discussion.topic.index', $topic));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->viewData['edit'] = [
            'parents' => Topic::where('contents.key', 'discussion-topic')->get(),
        ];

        return parent::contentEdit($id);
    }

    /**
     * Update a category using the parent method
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return parent::contentUpdate($request, $id);
    }

    /**
     * Set the status of the resource to approved
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        $topic = $this->bound($id);

        $topic->offBit(Topic::DELETED);
        $topic->onBit(Topic::APPROVED)->update();

        return redirect()->back();
    }

    /**
     * Set the status of the resource to declined
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function decline(Request $request, $id)
    {
        $topic = $this->bound($id);

        $topic->offBit(Topic::APPROVED);
        $topic->onBit(Topic::DELETED)->update();

        return redirect()->back();
    }
}