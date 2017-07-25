@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="comments icon"></i>
        <div class="content">
            {{ ___('Discussion Topics') }}
            <div class="sub header">{{ ___('Manage the discussion topics of the system.') }}</div>
        </div>
    </h1>
@endsection
