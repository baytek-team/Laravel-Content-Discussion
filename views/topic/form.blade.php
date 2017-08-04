@if($topic)
    <input type="hidden" name="id" value="{{ $topic->id }}">
@endif

<div class="field{{ $errors->has('title') ? ' error' : '' }}">
    <label for="title" class="floatl__label">{{ ___('Title') }}</label>
    <input type="text" id="title" name="title" placeholder="Title" value="{{ old('title', $topic->title) }}">
</div>
<div class="field{{ $errors->has('content') ? ' error' : '' }}">
    <label for="content" class="floatl__label">{{ ___('Content') }}</label>
    <textarea id="content" name="content" placeholder="Content">{{ old('content', $topic->content) }}</textarea>
</div>

@section('head')
{{-- <link rel="stylesheet" type="text/css" href="/css/trix.css"> --}}
{{-- <script type="text/javascript" src="/js/trix.js"></script> --}}
@endsection