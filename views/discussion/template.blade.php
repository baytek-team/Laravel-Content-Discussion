@extends('Content::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="comments icon"></i>
        <div class="content">
            {{ ___('Discussions') }}
            <div class="sub header">{{ ___('Manage the discussions of the system.') }}</div>
        </div>
    </h1>
@endsection
