<?php

namespace App\Events;

use App\Models\Photo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhotoCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Photo $photo) {}

    public function broadcastOn(): Channel
    {
        return new Channel('photos');
    }

    public function broadcastAs(): string
    {
        return 'created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->photo->id,
            'qr_token' => $this->photo->qr_token,
            'public_url' => $this->photo->public_url,
            'thumb_url' => $this->photo->thumb_url,
            'status' => $this->photo->status,
            'uploaded_at' => optional($this->photo->uploaded_at)->toISOString(),
        ];
    }
}

