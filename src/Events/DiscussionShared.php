<?php

namespace Baytek\Laravel\Content\Types\Discussion\Events;

use Baytek\Laravel\Users\User;

use Illuminate\Support\Str;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

use Illuminate\Http\Request;
use Auth;

class DiscussionShared
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, $discussion, $topic)
    {
        //Get the user
        $user = User::where('email', $request->email)->first();

        //Maybe the user doesn't exist?
        if (empty($user)) {
            $user = new User($request->all());
        }

        //Get the sender
        $sender = Auth::user();

        $this->type = 'DiscussionShared';
        $this->title = $sender->name . ' has shared a discussion with you / ' . $sender->name . ' a partagé une discussion avec vous';
        $this->user = $user;
        $this->parameters = [
            'discussion' => $discussion,
            'topic' => $topic,
            'sender_message' => $request->message
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('eohu');
    }
}
