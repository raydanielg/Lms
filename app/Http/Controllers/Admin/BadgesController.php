<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Translation\BadgeTranslation;
use Illuminate\Http\Request;

class BadgesController extends Controller
{
    public function index()
    {
        $this->authorize('admin_users_badges');

        removeContentLocale();

        $badges = Badge::all();
        foreach ($badges as $badge) {
            $badge->condition = json_decode($badge->condition);
        }

        $data = [
            'pageTitle' => trans('admin/main.badges'),
            'badges' => $badges->groupBy('type')
        ];

        return view('admin.users.badges', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_users_badges');

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'type' => 'required',
            'condition' => 'required|array',
            'condition.*' => 'required',
        ]);

        $data = $request->all();

        $badge = Badge::create([
            'image' => $data['image'],
            'type' => $data['type'],
            'condition' => json_encode($data['condition']),
            'score' => $data['score'] ?? null,
            'created_at' => time(),
        ]);

        BadgeTranslation::updateOrCreate([
            'badge_id' => $badge->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        return redirect(url()->previous() . '?tab=' . $data['type']);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_users_badges_edit');

        $badge = Badge::findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $badge->getTable(), $badge->id);


        $badge->condition = json_decode($badge->condition);

        $data = [
            'pageTitle' => trans('admin/pages/users.badges'),
            'badge' => $badge
        ];

        return view('admin.users.badges', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_users_badges_edit');

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'condition' => 'required|array',
            'condition.*' => 'required',
        ]);

        $data = $request->all();
        $badge = Badge::findOrFail($id);

        $badge->update([
            'image' => $data['image'],
            'condition' => json_encode($data['condition']),
            'score' => $data['score'] ?? null,
        ]);

        BadgeTranslation::updateOrCreate([
            'badge_id' => $badge->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/users/badges');
    }

    public function delete($id)
    {
        $this->authorize('admin_users_badges_delete');

        $badge = Badge::findOrFail($id);
        $badge->delete();

        return back();
    }
}
