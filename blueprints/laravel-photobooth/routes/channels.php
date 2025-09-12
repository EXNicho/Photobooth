<?php

use Illuminate\Support\Facades\Broadcast;

// Public channel for new photos broadcast
Broadcast::channel('photos', function () {
    return true;
});

