@extends('discussions::discussion.template')

@section('page.head.menu')
    @if($discussion->hasStatus($discussion::APPROVED))
        <a class="ui disabled primary button">
            <i class="checkmark icon"></i>
            {{ ___('Approved') }}
        </a>
    @else
        @button(___('Approve'), [
            'method' => 'post',
            'location' => 'discussion.approve',
            'type' => 'route',
            'class' => 'ui primary action button',
            'prepend' => '<i class="checkmark icon"></i>',
            'model' => $discussion,
            'confirm' => ___('Approve this discussion?')
        ])
    @endif
    @can('Delete Discussion')
        @if($discussion->hasStatus($discussion::DELETED))
            <a class="ui disabled button">
                <i class="delete icon"></i>
                {{ ___('Deleted') }}
            </a>
        @else
            @button(___('Delete'), [
                'method' => 'post',
                'location' => 'discussion.decline',
                'type' => 'route',
                'class' => 'ui action negative button',
                'prepend' => '<i class="delete icon"></i>',
                'model' => $discussion,
                'confirm' => ___('Delete/Reject this discussion?')
            ])
        @endif
    @endcan
@endsection

    @section('content')
        <form action="{{ route('discussion.update', $discussion->id) }}" method="POST" class="ui form">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            @include('discussions::discussion.form')
            {{-- <div class="field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" name="notify" tabindex="0" class="hidden">
                    <label for="notify">{{ ___('Notify members of discussion updates') }}</label>
                </div>
            </div> --}}
            <div class="ui hidden divider"></div>

            <div class="ui hidden error message"></div>
            <div class="field actions">
                <a class="ui button" href="{{ route('discussion.index') }}">{{ ___('Cancel') }}</a>

                <button type="submit" class="ui right floated primary button">
                    {{ ___('Update') }}
                </button>
            </div>
        </form>
@endsection