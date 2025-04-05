<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Ticket;
use App\Models\Translation\TicketTranslation;
use App\Models\Webinar;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Validator;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:64',
            'date' => 'required',
            'discount' => 'required',
            'capacity' => 'nullable',
        ]);


        $data = $request->all();
        $creator = null;

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::findOrFail($data['webinar_id']);

            $creator = $webinar->creator;
        } else if (!empty($data['bundle_id'])) {
            $bundle = Bundle::findOrFail($data['bundle_id']);

            $creator = $bundle->creator;
        }

        $date = $data['date'];
        $date = explode(' - ', $date);
        $startDate = convertTimeToUTCzone($date[0], getTimezone())->getTimestamp();
        $endDate = convertTimeToUTCzone($date[1], getTimezone())->getTimestamp();

        if (!empty($creator)) {
            $ticket = Ticket::create([
                'creator_id' => $creator->id,
                'webinar_id' => !empty($data['webinar_id']) ? $data['webinar_id'] : null,
                'bundle_id' => !empty($data['bundle_id']) ? $data['bundle_id'] : null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'discount' => $data['discount'],
                'capacity' => $data['capacity'],
                'created_at' => time()
            ]);

            if (!empty($ticket)) {
                TicketTranslation::updateOrCreate([
                    'ticket_id' => $ticket->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                ]);
            }
        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');


        $ticket = Ticket::select('id', 'capacity', 'discount', 'end_date', 'start_date')
            ->where('id', $id)
            ->first();

        if (!empty($ticket)) {
            $locale = $request->get('locale', app()->getLocale());
            if (empty($locale)) {
                $locale = app()->getLocale();
            }
            storeContentLocale($locale, $ticket->getTable(), $ticket->id);

            $ticket->title = $ticket->getTitleAttribute();
            $ticket->locale = mb_strtoupper($locale);

            return response()->json([
                'ticket' => $ticket->toArray()
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $this->validate($request, [
            'title' => 'required|max:64',
            'date' => 'required',
            'discount' => 'required|integer',
            'capacity' => 'nullable|integer',
        ]);
        $data = $request->all();

        $ticket = Ticket::find($id);

        if (!empty($ticket)) {

            $date = $data['date'];
            $date = explode(' - ', $date);
            $startDate = convertTimeToUTCzone($date[0], getTimezone())->getTimestamp();
            $endDate = convertTimeToUTCzone($date[1], getTimezone())->getTimestamp();

            $ticket->update([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'discount' => $data['discount'],
                'capacity' => $data['capacity'],
                'updated_at' => time()
            ]);

            TicketTranslation::updateOrCreate([
                'ticket_id' => $ticket->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
            ]);
        }

        removeContentLocale();

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        Ticket::find($id)->delete();

        return back();
    }
}
