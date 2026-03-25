<?php
namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
class ScanLogs extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'scan_logs';
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'UserID',
        'Mode',
        'lat',
        'VerificationCode',
        'lng'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'UserID');
    }
}
