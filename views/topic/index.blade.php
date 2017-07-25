@extends('discussions::topic.template')

@section('page.head.menu')
    <div class="ui secondary menu">
        <div class="right item">
            @if(Auth::user()->can('Create Discussion'))
            <a class="ui primary button" href="{{ route('discussion.topic.create') }}">
                <i class="add icon"></i>{{ ___('Add Topic') }}
            </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="ui text menu">
    <div class="header item">
        <i class="filter icon"></i>
        {{ ___('Filter By') }}
    </div>
    <a class="item @if($filter && $filter == 'active') active @endif" href="{{ route('discussion.topic.index') }}">{{ ___('Active') }}</a>
    <a class="item @if($filter && $filter == 'pending') active @endif" href="{{ route('discussion.topic.pending') }}">{{ ___('Pending') }}</a>
    <a class="item @if($filter && $filter == 'deleted') active @endif" href="{{ route('discussion.topic.deleted') }}">{{ ___('Deleted') }}</a>
</div>
<table class="ui selectable table">
    <thead>
        <tr>
            <th class="seven wide">{{ ___('Title') }}</th>
            <th>{{ ___('Date Created') }}</th>
            <th class="center aligned collapsing">{{ ___('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($topics as $topic)
            <tr class="seven wide" data-topic-id="{{ $topic->id }}">
                <td>{{ $topic->title }}</td>

                <td>{{ $topic->created_at }}</td>
                <td class="right aligned collapsing">
                    <div class="ui compact text menu">
                        <a href="{{ route( 'discussion.index', $topic->id) }}" class="item"><i class="comments icon"></i> {{ ___('Discussions') }}</a>
                        <a href="{{ route('discussion.topic.edit', $topic->id) }}" class="item"><i class="pencil icon"></i> {{ ___('En Button') }}</a>
                        @if(is_null($topic->translation()))
                            <a class="item" href="{{ route('translation.edit', $topic->id) }}">
                                <i class="add icon"></i>
                                {{ ___('Fr Button') }}
                            </a>
                        @else
                            <a class="item" href="{{ route('discussion.topic.edit', $topic->translation()) }}">
                                <i class="pencil icon"></i>
                                {{ ___('Fr Button') }}
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">
                    <div class="ui centered">{{ ___('There are no results') }}</div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $topics->links('pagination.default') }}

@endsection