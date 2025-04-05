<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AgoraHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\AgoraHistory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AgoraHistoryController extends Controller
{
    public function index()
    {
        $this->authorize('admin_agora_history_list');

        $agoraHistories = AgoraHistory::whereNotNull('end_at')
            ->orderBy('start_at')
            ->with([
                'session' => function ($query) {
                    $query->with('webinar');
                }
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.agora_history'),
            'agoraHistories' => $agoraHistories
        ];

        return view('admin.agora_history.index', $data);
    }

    public function exportExcel()
    {
        $agoraHistories = AgoraHistory::whereNotNull('end_at')
            ->orderBy('start_at')
            ->with([
                'session' => function ($query) {
                    $query->with('webinar');
                }
            ])
            ->get();

        $export = new AgoraHistoryExport($agoraHistories);

        return Excel::download($export, 'agoraHistory.xlsx');
    }
}
