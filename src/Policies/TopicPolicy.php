<?php

namespace Baytek\Laravel\Content\Types\Discussion\Policies;

use Baytek\Laravel\Content\Policies\GeneralPolicy;

use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy extends GeneralPolicy
{
    public $contentType = 'Topic';
}
