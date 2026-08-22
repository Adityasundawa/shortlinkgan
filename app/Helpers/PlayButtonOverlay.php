<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Image;

class PlayButtonOverlay
{
    /**
     * Overlay the play icon (public/thumb/play.png) onto the center of an
     * already-saved image file, in place.
     */
    public static function apply(string $fullPath): void
    {
        try {
            $thumbnail = Image::make(public_path('thumb/play.png'))->fit(100, 100);
            $image = Image::make($fullPath);
            $image->insert($thumbnail, 'center', 0, 0);
            $image->save($fullPath);
        } catch (\Throwable $e) {
            Log::error('Play button overlay failed: '.$e->getMessage());
        }
    }
}
