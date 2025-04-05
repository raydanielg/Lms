<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Webinar;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function toggle(Request $request, $id)
    {
        $userId = auth('api')->id();

        $webinar = Webinar::where('id', $id)
            ->where('status', 'active')
            ->first();
        if (!$webinar) {
            abort(404);
        }

        $isFavorite = Favorite::where('webinar_id', $webinar->id)
            ->where('user_id', $userId)
            ->first();

        if (empty($isFavorite)) {
            Favorite::create([
                'user_id' => $userId,
                'webinar_id' => $webinar->id,
                'created_at' => time()
            ]);
            $status = 'favored';
        } else {
            $isFavorite->delete();
            $status = 'unfavored';

        }
        return apiResponse2(1, $status, trans('favorite.' . $status));
    }


}
