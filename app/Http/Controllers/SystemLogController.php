<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Constants\LogLevel;
use Kd9703\Eloquents\Support\SystemLog;

class SystemLogController extends BaseController
{

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $system_logs = SystemLog::query()
            ->orderBy('id', 'desc')
            ->whereIn('level', [
                LogLevel::DEBUG,
                LogLevel::INFO,
                LogLevel::NOTICE,
                LogLevel::MEDIA_ACCESS,
                LogLevel::JOB,
                LogLevel::WARNING,
                LogLevel::ERROR,
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY,
            ])
            ->paginate($request->per_page ?? 100);

        return view('log.index', [
            'logname' => 'system_logs',
            'logs'    => $system_logs,
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     */
    public function show($id, Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $system_log = SystemLog::findOrFail($id);

        return view('log.detail', [
            'logname' => 'system_logs',
            'log'     => $system_log,
        ]);
    }

}
