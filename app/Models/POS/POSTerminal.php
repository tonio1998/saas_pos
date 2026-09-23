<?php

namespace App\Models\POS;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class POSTerminal extends Model
{
    use BelongsToTenant;

    protected $table = 'pos_terminals';

    protected $fillable = [
        'tenant_id',
        'terminal_name',
        'terminal_code',
        'drawer_id',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'archived',
    ];
    protected static function booted(): void
    {
        static::created(function ($terminal) {
            if (empty($terminal->terminal_code)) {
                $terminal->updateQuietly([
                    'terminal_code' => generateTerminalCode($terminal->id),
                ]);
            }
        });
    }

    public function drawer()
    {
        return $this->belongsTo(
            POSCashDrawer::class,
            'drawer_id'
        );
    }
}
