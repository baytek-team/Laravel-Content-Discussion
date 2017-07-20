<div class="field{{ $errors->has('title') ? ' error' : '' }}">
    <label for="title">{{ ___('Title') }}</label>
    <input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $discussion->title) }}" required>
</div>
<div class="field{{ $errors->has('content') ? ' error' : '' }}">
    <label for="content">{{ ___('Content') }}</label>
    <textarea id="content" name="content" placeholder="Content">{{ strip_tags(old('content', $discussion->content)) }}</textarea>
</div>
<div class="field">
    <label for="parent_id">{{ ___('Topic') }}</label>
    <div class="ui fluid dropdown labeled search icon basic button">
        <input type="hidden" name="parent_id" value="{{ old('parent_id', isset($parent_id)?$parent_id:'')}}">
        <i class="search icon"></i>
        <span class="text">{{ ___('Click to choose a topic') }}</span>
        <div class="menu transition hidden">
            @foreach($topics as $item)
                <div class="item" data-value="{{ $item->id }}">{{ $item->title }}</div>
            @endforeach
        </div>
    </div>
</div>

<div class="ui hidden divider"></div>

@section('head')
{{-- <link rel="stylesheet" type="text/css" href="/css/trix.css"> --}}
{{-- <script type="text/javascript" src="/js/trix.js"></script> --}}
@endsection