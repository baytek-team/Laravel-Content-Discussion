@extends('discussions::discussion.template')

@section('page.head.menu')
    <div class="ui secondary contextual menu">
        <div class="header item">
            <i class="filter icon"></i>
            {{ ___('Filter By') }}
        </div>
        <a class="item @if($filter && $filter == 'active') active @endif" href="{{ route('discussion.index') }}">{{ ___('Active') }}</a>
        <a class="item @if($filter && $filter == 'deleted') active @endif" href="{{ route('discussion.deleted') }}">{{ ___('Deleted') }}</a>
        <div class="item">
            @can('Create Discussion'))
            <a class="ui primary button" href="{{ route('discussion.create') }}">
                <i class="add icon"></i>{{ ___('Add Discussion') }}
            </a>
            @endcan
        </div>
    </div>
@endsection
@if(count($discussions))
    @section('content')
    <table class="ui selectable very basic table">
        <thead>
            <tr>
                <th class="nine wide">{{ ___('Discussion') }}</th>
                <th>{{ ___('Date Posted') }}</th>
                <th class="center aligned collapsing">{{ ___('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($discussions as $discussion)
                <tr class="seven wide" data-discussion-id="{{ $discussion->id }}">
                    <td>
                        <div class="ui list">
                            <div class="item">
                                <div class="content">
                                    <div class="header">
                                        @if($discussion->metadata('author_id'))
                                            {{-- <a href="{{ route( 'members.edit', $discussion->metadata('author_id')) }}"> --}}{{ $discussion->metadata('author_id')->name }}{{-- </a> --}}
                                        @else
                                            {{ __('Unknown Author') }}
                                        @endif
                                        {{ ___('posted in') }}
                                        @if($discussion->topic)
                                            <a href="{{ route( 'discussion.index', $discussion->topic->id) }}">{{ $discussion->topic->title }}</a>
                                        @else
                                            {{ ___('Parent not found') }}
                                        @endif
                                    </div>
                                    <div class="description">{{ str_limit($discussion->title, 200) }}</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $discussion->created_at }}</td>
                    <td class="right aligned collapsing">
                        <div class="ui compact text menu">
                            <a href="{{ route('discussion.children', $discussion->id) }}" class="item"><i class="comments icon"></i>{{-- {{ ___('Responses') }} --}}</a>
                            @can('Update Discussion')
                            <a href="{{ route('discussion.edit', $discussion->id) }}" class="item"><i class="pencil icon"></i>{{-- {{ ___('Edit') }} --}}</a>
                            @endcan
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

    {{ $discussions->links('pagination.default') }}

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