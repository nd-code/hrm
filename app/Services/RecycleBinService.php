<?php

namespace App\Services;

use App\Models\RecycleBin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class RecycleBinService
{
    /**
     * Move record to recycle bin
     */
    public static function delete(
        Model $model,
        string $module
    ): bool {
        RecycleBin::create([
            'module'     => $module,
            'record_id'  => $model->id,
            'data'       => $model->toArray(),
            'deleted_by' => Auth::id(),
        ]);

        return $model->delete(); // OR forceDelete() if needed
    }

    /**
     * Restore record from recycle bin
     */
    public static function restore(int $recycleId): bool
    {
        $item = RecycleBin::findOrFail($recycleId);

        $modelClass = self::resolveModel($item->module);

        $modelClass::create($item->data);

        $item->delete();

        return true;
    }

    /**
     * Permanent delete
     */
    public static function forceDelete(int $recycleId): bool
    {
        return RecycleBin::findOrFail($recycleId)->delete();
    }

    /**
     * Resolve module to model
     */
    protected static function resolveModel(string $module): string
    {
        return match ($module) {
            'employee' => \App\Models\Employee::class,
            'user'     => \App\Models\User::class,
            'assessment'     => \App\Models\Assessment::class,
            'candidate'     => \App\Models\Candidate::class,
            'leave'     => \App\Models\Leave::class,
            'reminder'     => \App\Models\Reminder::class,
            'feedback'     => \App\Models\Review::class,
            'team'     => \App\Models\Team::class,
            'vendor'     => \App\Models\Vendor::class,
            default     => abort(404, 'Invalid module'),
        };
    }
}
