<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class POSTerminal extends Model
{
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
        static::creating(function ($terminal) {
            $terminal->updateQuietly([
                'terminal_code' => generateTerminalCode($terminal->id),
            ]);
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
