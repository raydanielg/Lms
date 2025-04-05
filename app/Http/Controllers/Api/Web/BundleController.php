<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Models\AdvertisingBanner;
use App\Models\Api\Bundle;
use App\Models\Favorite;
use App\Models\Webinar;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = Bundle::with([
            "badges"=> function ($query) {
                $query->where('targetable_type', 'App\Models\Bundle');
                $query->with([
                    'badge'=>function ($query) {
                        $time = time();
                        $query->where('enable', true);

                        $query->where(function ($query) use ($time) {
                            $query->whereNull('start_at');
                            $query->orWhere('start_at', '<', $time);
                        });

                        $query->where(function ($query) use ($time) {
                            $query->whereNull('end_at');
                            $query->orWhere('end_at', '>', $time);
                        });
                    }
                ]);
            },
        ])->
        where('status', 'active')->get();
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'bundles' => BundleResource::collection($bundles)
            ]
        );
    }

    public function show($id)
    {
        $user = apiAuth();
        $bundle = Bundle::where('id', $id)
            ->with([
                'tickets' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'bundleWebinars' => function ($query) {
                    $query->with([
                        'webinar' => function ($query) {
                            $query->where('status', Webinar::$active);
                        }
                    ]);
                },
                'reviews' => function ($query) {
                    $query->where('status', 'active');
                    $query->with([
                        'comments' => function ($query) {
                            $query->where('status', 'active');
                        },
                        'creator' => function ($qu) {
                            $qu->select('id', 'full_name', 'avatar');
                        }
                    ]);
                },
                'comments' => function ($query) {
                    $query->where('status', 'active');
                    $query->whereNull('reply_id');
                    $query->with([
                        'user' => function ($query) {
                            $query->select('id', 'full_name', 'role_name', 'role_id', 'avatar', 'avatar_settings');
                        },
                        'replies' => function ($query) {
                            $query->where('status', 'active');
                            $query->with([
                                'user' => function ($query) {
                                    $query->select('id', 'full_name', 'role_name', 'role_id', 'avatar', 'avatar_settings');
                                }
                            ]);
                        }
                    ]);
                    $query->orderBy('created_at', 'desc');
                },
            ])
            ->withCount([
                'sales' => function ($query) {
                    $query->whereNull('refund_at');
                }
            ])
            ->where('status', 'active')
            ->first();

        if (!$bundle) {
            abort(404);
        }

        $isFavorite = false;

        if (!empty($user)) {
            $isFavorite = Favorite::where('bundle_id', $bundle->id)
                ->where('user_id', $user->id)
                ->first();
        }


        $resource = new BundleResource($bundle);
        $resource->show = true;

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'bundle' => $resource,

            ]);
    }

}
