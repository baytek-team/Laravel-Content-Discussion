@extends('discussions::discussion.template')

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <form action="{{route('discussion.store')}}" method="POST" class="ui form">
                {{ csrf_field() }}

                @include('discussions::discussion.form')
                <div class="field">
                    <div class="ui toggle checkbox">
                        <input type="checkbox" name="notify" tabindex="0" class="hidden" @if(old('notify')) checked="checked" @endif>
                        <label for="notify">{{ ___('Notify members of new discussion') }}</label>
                    </div>
                </div>
                <div class="ui hidden divider"></div>
                <div class="ui hidden divider"></div>

                <div class="field actions">
    	            <a class="ui button" href="{{ route('discussion.index') }}">{{ ___('Cancel') }}</a>
    	            <button type="submit" class="ui right floated primary button">
    	            	{{ ___('Create') }}
                	</button>
                </div>
            </form>
        </div>
    </div>
@endsection