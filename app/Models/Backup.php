<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Backup extends Model  implements AuditableContract
{
    use SoftDeletes;
    use Auditable;
    protected $table = 'backups';

    protected $fillable = [
        'filename',
        'filepath',
        'backup_type',
        'file_size',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_by' => 'integer',
    ];
}
