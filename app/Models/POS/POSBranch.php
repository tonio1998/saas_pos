<?php

namespace App\Models\POS;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSBranch extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'pos_branches';

    protected $fillable = [
        'tenant_id',
        'branch_name',
        'branch_code',
        'address',
        'phone',
        'is_main_branch',
        'status',
        'archived',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_main_branch' => 'boolean',
        'archived'       => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(POSTenant::class, 'tenant_id');
    }
}
