<?php

namespace App\Http\Controllers\AuditLog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    //


     public function index()
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->paginate(50);

        return view('dashboard.audit.index', compact('logs'));
    }
}
