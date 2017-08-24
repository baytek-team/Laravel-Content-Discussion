@extends('discussions::topic.template')

@section('page.head.menu')
    <div class="ui secondary contextual menu">
        <div class="header item">
            <i class="filter icon"></i>
            {{ ___('Filter By') }}
        </div>
        <a class="item @if($filter && $filter == 'active') active @endif" href="{{ route('discussion.topic.index') }}">{{ ___('Active') }}</a>
        <a class="item @if($filter && $filter == 'pending') active @endif" href="{{ route('discussion.topic.pending') }}">{{ ___('Pending') }}</a>
        <a class="item @if($filter && $filter == 'deleted') active @endif" href="{{ route('discussion.topic.deleted') }}">{{ ___('Deleted') }}</a>

        <div class="item">
            @can('Create Topic')
                <a class="ui primary button" href="{{ route('discussion.topic.create') }}">
                    <i class="add icon"></i>{{ ___('Add Topic') }}
                </a>
            @endcan
        </div>
    </div>
@endsection

@if(count($topics))
    @section('content')
    <table class="ui selectable very basic table">
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
                            @can('View Discussion')
                            <a href="{{ route( 'discussion.index') }}" class="item"><i class="comments icon"></i>
                            @endcan
                            {{-- {{ ___('Discussions') }} --}}</a>
                            @can('Update Topic')
                            <a href="{{ route('discussion.topic.edit', $topic->id) }}" class="item"><i class="pencil icon"></i> {{-- {{ ___('En Button') }} --}}</a>
                            @endcan
                            {{-- @if(is_null($topic->translation()))
                                <a class="item" href="{{ route('translation.edit', $topic->id) }}">
                                    <i class="add icon"></i>
                                    {{ ___('Fr Button') }}
                                </a>
                            @else
                                <a class="item" href="{{ route('discussion.topic.edit', $topic->translation()) }}">
                                    <i class="pencil icon"></i>
                                    {{ ___('Fr Button') }}
                                </a>
                            @endif --}}
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
@else
    @section('outer-content')
        <div class="ui middle aligned padded grid no-result">
            <div class="column">
                <div class="ui center aligned padded grid">
                    <div class="column">
                        <h2>{{ ___('We couldn\'t find anything') }}</h2>
                    </div>
                </div>
            </div>
        </div>
    @endsection
@endif