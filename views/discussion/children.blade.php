@extends('discussions::discussion.template')

@section('page.head.menu')
    <div class="ui secondary contextual menu">
        <div class="header item">
            <i class="filter icon"></i>
            {{ ___('Filter By') }}
        </div>
        <a class="item @if($filter && $filter == 'active') active @endif" href="{{ route('discussion.index') }}">{{ ___('Active') }}</a>
        <a class="item @if($filter && $filter == 'deleted') active @endif" href="{{ route('discussion.deleted') }}">{{ ___('Deleted') }}</a>
    </div>
@endsection

@section('content')
<h2 class="ui header">
    {{ ___('Responses to:') }}
    <div class="sub header">{{ $parentDiscussion->title }}</div>
</h2>
<div class="ui hidden divider"></div>

<table class="ui selectable very basic table">
    <thead>
        <tr>
            <th class="nine wide">{{ ___('Response') }}</th>
            <th>{{ ___('Date Posted') }}</th>
            <th class="center aligned collapsing">{{ ___('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($discussions as $discussion)
            <tr class="seven wide @if($discussion->hasStatus($discussion::DELETED)) error @endif" data-discussion-id="{{ $discussion->id }}">
                <td>
                    <div class="ui list">
                        <div class="item">
                            <div class="content">
                                <div class="header">
                                    @if($discussion->metadata('author_id'))
                                        @can('Update Member')
                                        <a href="{{ route( 'members.edit', $discussion->metadata('author_id')) }}">{{ $discussion->metadata('author_id')->name }}</a>
                                        @else
                                        {{ $discussion->metadata('author_id')->name }}
                                        @endcan
                                    @else
                                        {{ __('Unknown Author') }}
                                    @endif
                                    {{ ___('responded') }}
                                </div>
                                <div class="description">{{ str_limit($discussion->content, 200) }}</div>
                            </div>
                        </div>
                    </div>
                </td>
                <td>{{ $discussion->created_at }}</td>
                <td class="right aligned collapsing">
                    <a href="{{ route('discussion.children', $discussion->id) }}" class="ui icon @if($discussion->hasStatus($discussion::DELETED)) basic negative @endif button">
                        <i class="comments icon"></i>
                        {{ ___('Responses') }}
                    </a>
                    @can('Update Discussion')
                    <a href="{{ route('discussion.editResponse', $discussion->id) }}" class="ui icon @if($discussion->hasStatus($discussion::DELETED)) basic negative @endif button">
                        <i class="pencil icon"></i>
                        {{ ___('Edit') }}
                    </a>
                    @endcan
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