<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Role;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $data = [];

        $search = $request->get('search', null);

        if (!empty($search) and strlen($search) >= 3) {
            $webinars = Webinar::where('status', 'active')
                ->where('private', false)
                ->whereTranslationLike('title', "%$search%")
                ->with([
                    'teacher' => function ($query) {
                        $query->select('id', 'full_name', 'avatar', 'avatar_settings');
                    },
                    'reviews'
                ])
                ->get();

            $products = Product::where('status', 'active')
                ->whereTranslationLike('title', "%$search%")
                ->with([
                    'creator' => function ($query) {
                        $query->select('id', 'full_name', 'avatar', 'avatar_settings');
                    }
                ])
                ->get();

            $users = User::where('status', 'active')
                ->where('full_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('mobile', 'like', "%$search%")
                ->with([
                    'webinars' => function ($query) {
                        $query->where('status', 'active');
                        //dd(getBindedSQL($query));
                    }
                ])
                ->get();

            $teachers = $users->where('role_name', Role::$teacher);
            $organizations = $users->where('role_name', Role::$organization);

            $seoSettings = getSeoMetas('search');
            $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('site.search_page_title');
            $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('site.search_page_title');
            $pageRobot = getPageRobot('search');

            $data = [
                'pageTitle' => $pageTitle,
                'pageDescription' => $pageDescription,
                'pageRobot' => $pageRobot,
                'resultCount' => count($webinars) + count($teachers) + count($organizations),
                'webinars' => $webinars,
                'teachers' => $teachers,
                'organizations' => $organizations,
                'products' => $products,
            ];
        }

        return view(getTemplate() . '.pages.search', $data);
    }
}
