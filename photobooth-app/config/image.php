<?php

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

return [
    // Force GD to avoid Imagick extension requirement
    'driver' => env('IMAGE_DRIVER', 'gd'),

    // Driver class map (for Intervention Image v3)
    'drivers' => [
        'gd' => GdDriver::class,
        'imagick' => ImagickDriver::class,
    ],
];

