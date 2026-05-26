<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Auditable;
use Spatie\Permission\Models\Role as SpatieRole;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
class Role extends SpatieRole implements AuditableContract
{
    use HasFactory;
    use Auditable;

    public $table = 'roles';
    public $primaryKey = 'id';
    public $filterable = [
        'id',
        'name',
        'details',
        'guard_name',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
