<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\Web\WebinarController;
use App\Models\Api\Favorite;
use App\Models\Api\Webinar;
use App\Models\Bundle;
use App\User;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function toggle(Request $request, $id)
    {
        $userId = apiAuth()->id;

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

    public function toggle2(Request $request)
    {
        validateParam($request->all(), [
            'item' => 'required|in:bundle,webinar',
            'id' => 'required'
        ]);

        $userId = apiAuth()->id;
        $item = $request->input('item');
        $id = $request->input('id');
        if ($item == 'webinar') {
            $itemObj = $webinar = Webinar::where('id', $id)
                ->where('status', 'active')
                ->first();
        } else if ($item == 'bundle') {
            $itemObj = Bundle::where('id', $id)
                ->where('status', 'active')
                ->first();
        }
        if (!$itemObj) {
            abort(404);
        }

        $isFavorite = Favorite::where($item . '_id', $itemObj->id)
            ->where('user_id', $userId)
            ->first();

        if (empty($isFavorite)) {
            Favorite::create([
                'user_id' => $userId,
                $item . '_id' => $itemObj->id,
                //  'webinar_id' => $webinar->id,
                'created_at' => time()
            ]);
            $status = 'favored';

        } else {

            $isFavorite->delete();
            $status = 'unfavored';

        }
        return apiResponse2(1, $status, trans('favorite.' . $status));
    }

    public function list()
    {
        $user = apiAuth();
        $favorites = Favorite::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $favorites = $favorites->map(function ($favorite) {
            return [
                'id' => $favorite->id,
                'webinar' => $favorite->webinar->brief,
                'created_at' => $favorite->created_at
            ];
        });
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            ['favorites' => $favorites]
        );
    }

    public function destroy($id)
    {
        $user = apiAuth();
        $favorite = favorite::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (empty($favorite)) {
            abort(404);
        }
        $favorite->delete();
        return apiResponse2(1, 'deleted', trans('api.public.deleted'));
    }
}
