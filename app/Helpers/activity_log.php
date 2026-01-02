<?php

use App\Models\ActivityLog;

function activityLog(
    string $module,
    string $action,
    $recordId = null,
    $old = null,
    $new = null
) {
    $user = auth()->user() ?? auth('employee')->user();

    ActivityLog::create([
        'user_id'    => $user?->id,
        'user_type'  => $user ? get_class($user) : null,
        'module'     => $module,
        'action'     => $action,
        'record_id'  => $recordId,
        'old_values' => $old,
        'new_values' => $new,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}