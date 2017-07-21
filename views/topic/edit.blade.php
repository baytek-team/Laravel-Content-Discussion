@extends('topic::template')

@section('page.head.menu')
    @if($topic->hasStatus($topic::APPROVED))
        <a class="ui disabled primary button">
            <i class="checkmark icon"></i>
            {{ ___('Approved') }}
        </a>
    @elseif($topic->hasStatus($topic::DELETED))
        <a class="ui disabled button">
            <i class="delete icon"></i>
            {{ ___('Deleted') }}
        </a>
    @else
        @button(___('Approve'), [
            'method' => 'post',
            'location' => 'discussion.topic.approve',
            'type' => 'route',
            'class' => 'ui primary action button',
            'prepend' => '<i class="checkmark icon"></i>',
            'model' => $topic,
            'confirm' => ___('Approve this discussion topic? This action cannot be undone.')
        ])

        @button(___('Delete'), [
            'method' => 'post',
            'location' => 'discussion.topic.decline',
            'type' => 'route',
            'class' => 'ui action button',
            'prepend' => '<i class="delete icon"></i>',
            'model' => $topic,
            'confirm' => ___('Delete/Reject this discussion topic? This action cannot be undone.')
        ])
    @endif

    @endsection

    @section('content')
    <div id="registration" class="ui container">
        <div class="ui hidden divider"></div>
        <form action="{{ route('discussion.topic.update', $topic->id) }}" method="POST" class="ui form">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            @include('admin.discussions.topic.form')
            {{-- <div class="field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" name="notify" tabindex="0" class="hidden">
                    <label for="notify">{{ ___('Notify members of discussion updates') }}</label>
                </div>
            </div> --}}
            <div class="ui hidden divider"></div>

            <div class="ui hidden error message"></div>
            <div class="field actions">
                <a class="ui button" href="{{ route('discussion.topic.index') }}">{{ ___('Cancel') }}</a>

                <button type="submit" class="ui right floated primary button">
                    {{ ___('Update') }}
                </button>
            </div>
        </form>
    </div>
@endsection