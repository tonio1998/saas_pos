<?php

namespace App\Services;

use App\Models\Audit;
use Illuminate\Http\Request;

class AuditLogService
{
    public function query(Request $request)
    {
        return Audit::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->select([
                'id',
                'user_id',
                'event',
                'auditable_type',
                'ip_address',
                'created_at',
                'url',
                'old_values',
                'new_values'
            ]);
    }

    public function severity(
        string $event
    ): string {

        return match ($event) {

            'created' => 'success',

            'updated' => 'warning',

            'deleted' => 'danger',

            default => 'secondary',
        };
    }
}
