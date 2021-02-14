<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Kd9703\Constants\LogLevel;
use Kd9703\Eloquents\Support\OwnerLog;

class OwnerLogController extends BaseController
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $owner_logs = OwnerLog::query()
            ->orderBy('id', 'desc')
            ->whereIn('level', [
                // LogLevel::DEBUG,
                LogLevel::INFO,
                LogLevel::NOTICE,
                // LogLevel::MEDIA_ACCESS,
                // LogLevel::JOB,
                LogLevel::WARNING,
                LogLevel::ERROR,
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY,
            ])
            ->paginate($request->per_page ?? 100);

        return view('log.owner-logs', [
            'logname'    => 'owner_logs',
            'owner_logs' => $owner_logs,
        ]);
    }
}
