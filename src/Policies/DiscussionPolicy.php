<?php

namespace Baytek\Laravel\Content\Types\Discussion\Policies;

use Baytek\Laravel\Content\Policies\GeneralPolicy;

use Illuminate\Auth\Access\HandlesAuthorization;

class DiscussionPolicy extends GeneralPolicy
{
    public $contentType = 'Discussion';
}
