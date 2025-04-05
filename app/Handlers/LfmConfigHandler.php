<?php

namespace App\Handlers;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler;

class LfmConfigHandler extends ConfigHandler
{
    public function userField()
    {
        $user = auth()->user();
        return $user->id;
    }
}

