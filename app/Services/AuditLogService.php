<?php

namespace App\Services;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditLogService
{
    public function query(Request $request)
    {
        return Audit::query()
            ->with('user')
            ->select([
                'id',
                'user_id',
                'event',
                'auditable_type',
                'ip_address',
                'created_at',
                'url'
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
