<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Ticket;
use App\Models\Translation\TicketTranslation;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Validator;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $canStore = false;
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $rules = [
            'title' => 'required|max:64',
            'sub_title' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'discount' => 'required|integer|between:1,100',
        ];

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::find($data['webinar_id']);

            if (!empty($webinar) and $webinar->canAccess($user)) {
                $canStore = true;

                $rules ['webinar_id'] = 'required';

                if (!empty($webinar->capacity)) {
                    $sumTicketsCapacities = $webinar->tickets->sum('capacity');
                    $capacity = $webinar->capacity - $sumTicketsCapacities;

                    $rules ['capacity'] = 'nullable|numeric|min:1|max:' . $capacity;
                }
            }
        } else if (!empty($data['bundle_id'])) {
            $bundle = Bundle::find($data['bundle_id']);

            if (!empty($bundle) and $bundle->canAccess($user)) {
                $canStore = true;
                $rules ['bundle_id'] = 'required';
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($canStore) {
            $ticket = Ticket::create([
                'creator_id' => $user->id,
                'webinar_id' => !empty($data['webinar_id']) ? $data['webinar_id'] : null,
                'bundle_id' => !empty($data['bundle_id']) ? $data['bundle_id'] : null,
                'start_date' => strtotime($data['start_date']),
                'end_date' => strtotime($data['end_date']),
                'discount' => $data['discount'],
                'capacity' => $data['capacity'] ?? null,
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

            return response()->json([
                'code' => 200,
            ], 200);
        }

        abort(403);
    }

    public function update(Request $request, $id)
    {
        $canStore = false;
        $user = auth()->user();

        $data = $request->get('ajax')[$id];

        $rules = [
            'title' => 'required|max:64',
            'sub_title' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'discount' => 'required|integer|between:1,100',
        ];

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::find($data['webinar_id']);

            if (!empty($webinar) and $webinar->canAccess($user)) {
                $canStore = true;

                $rules ['webinar_id'] = 'required';

                if (!empty($webinar->capacity)) {
                    $sumTicketsCapacities = $webinar->tickets->sum('capacity');
                    $capacity = $webinar->capacity - $sumTicketsCapacities;

                    $rules ['capacity'] = 'nullable|numeric|min:1|max:' . $capacity;
                }
            }
        } else if (!empty($data['bundle_id'])) {
            $bundle = Bundle::find($data['bundle_id']);

            if (!empty($bundle) and $bundle->canAccess($user)) {
                $canStore = true;
                $rules ['bundle_id'] = 'required';
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($canStore) {
            $columnName = !empty($data['webinar_id']) ? 'webinar_id' : 'bundle_id';
            $columnValue = !empty($data['webinar_id']) ? $data['webinar_id'] : $data['bundle_id'];

            $ticket = Ticket::where('id', $id)
                ->where(function ($query) use ($user, $columnName, $columnValue) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere($columnName, $columnValue);
                })
                ->first();

            if (!empty($ticket)) {
                $ticket->update([
                    'start_date' => strtotime($data['start_date']),
                    'end_date' => strtotime($data['end_date']),
                    'discount' => $data['discount'],
                    'capacity' => $data['capacity'] ?? null,
                    'updated_at' => time()
                ]);

                TicketTranslation::updateOrCreate([
                    'ticket_id' => $ticket->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        abort(403);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        $ticket = Ticket::where('id', $id)->first();

        if (!empty($ticket)) {
            $item = null;
            if (!empty($ticket->webinar_id)) {
                $item = Webinar::query()->find($ticket->webinar_id);
            } else if (!empty($ticket->bundle_id)) {
                $item = Bundle::find($ticket->bundle_id);
            }

            if ($ticket->creator_id == $user->id or (!empty($item) and $item->canAccess($user))) {
                $ticket->delete();

                return response()->json([
                    'code' => 200
                ], 200);
            }
        }

        return response()->json([], 422);
    }
}
