<?php


use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSSale;
use App\Models\QrCodes;
use App\Models\SmsQueuingModel;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Str;

function generateSaleInvoiceNo(): string
{
    return 'S' . now()->format('Ymd') . '-' . strtoupper(generateSerialNumber(6));
}

if (!function_exists('encryptId')) {
    function encryptId($id, int $times = 4): string
    {
        $value = (string) $id;

        for ($i = 0; $i < $times; $i++) {
            $value = base64_encode($value);
        }

        return rtrim(strtr($value, '+/', '-_'), '=');
    }
}

if (!function_exists('decryptId')) {
    function decryptId($value, int $times = 4): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = strtr($value, '-_', '+/');
        $value .= str_repeat('=', (4 - strlen($value) % 4) % 4);

        for ($i = 0; $i < $times; $i++) {
            $value = base64_decode($value, true);

            if ($value === false) {
                return null;
            }
        }

        return $value;
    }
}

function getCustomerCode(int $id): string
{
    return 'CUS-' . str_pad($id, 8, '0', STR_PAD_LEFT);
}



function generateSalesCode(int $tenantId): string
{
    $today = Carbon::today();

    $lastCode = POSSale::where('tenant_id', $tenantId)
        ->whereDate('created_at', $today)
        ->orderByRaw('CAST(SUBSTRING_INDEX(sale_code, "-", -1) AS UNSIGNED) DESC')
        ->value('sale_code');

    $next = $lastCode
        ? ((int) substr($lastCode, strrpos($lastCode, '-') + 1)) + 1
        : 1;

    return $today->format('ymd') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
}

function generateSerialNumber(int $length = 6): string
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

 function generateTerminalCode($id): string
 {
     return 'TCH-' . str_pad($id, 6, '0', STR_PAD_LEFT);
 }

function format_date($date)
{
    if(!$date){
        return 'N/A';
    }
    return $date->format('M d, Y h:i A');
}

if (!function_exists('system_settings')) {

    function system_settings()
    {
        return cache()->rememberForever(
            'system_settings',
            function () {

                return SystemSetting::first();
            }
        );
    }
}

function formatAuditMessage(
    $audit
): string {

    $user = optional(
        $audit->user
    )->name ?? 'System';

    $model = Str::of(
        class_basename(
            $audit->auditable_type
        )
    )
        ->snake()
        ->replace('_', ' ')
        ->singular()
        ->lower();

    $article = in_array(
        substr($model, 0, 1),
        ['a', 'e', 'i', 'o', 'u']
    )
        ? 'an'
        : 'a';

    return match ($audit->event) {

        'created' =>

            $user .
            ' created ' .
            $article .
            ' ' .
            $model,

        'updated' =>

            $user .
            ' updated ' .
            $article .
            ' ' .
            $model,

        'deleted' =>

            $user .
            ' deleted ' .
            $article .
            ' ' .
            $model,

        'restored' =>

            $user .
            ' restored ' .
            $article .
            ' ' .
            $model,

        default =>

            $user .
            ' performed an action on ' .
            $article .
            ' ' .
            $model,
    };
}

function queueSMSSend($phoneNumber, $message)
{
    $queue = new SmsQueuingModel();
    $queue->PhoneNumber = $phoneNumber;
    $queue->Message = $message;
    $queue->created_at = now();
    $queue->updated_at = now();
    $queue->status = 'active';
    $queue->archived = 0;
    $queue->created_by = 0;
    $queue->updated_by = 0;
    $queue->save();
}


function getCashDrawerCode(int $id): string
{
    return 'CDR-' . str_pad($id, 6, '0', STR_PAD_LEFT);
}

function generateCashShiftCode(int $id): string
{
    return 'CSH-' . str_pad($id, 6, '0', STR_PAD_LEFT);
}

function generateQrCode()
{
    $prefix = cache('school_settings_' . session('school_id'))?->SchoolCode;
    $schoolSettings = cache('school_settings_' . session('school_id'));
    $schoolCode = $schoolSettings?->id;

    $lastRow = QrCodes::where('prefix', $prefix)
        ->orderByDesc('last_number')
        ->first();

    $newNumber = ($lastRow?->last_number ?? 0) + 1;

    $qrCodeRow = new QrCodes();
    $qrCodeRow->school_id = $schoolCode;
    $qrCodeRow->prefix = $prefix;
//    $qrCodeRow->UserID = 0;
    $qrCodeRow->last_number = $newNumber;
    $qrCodeRow->created_by = 0;
    $qrCodeRow->updated_by = 0;
    $qrCodeRow->created_at = now();
    $qrCodeRow->updated_at = now();
    $qrCodeRow->status = 'active';
    $qrCodeRow->archived = 0;
    $qrCodeRow->save();

    return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
}

function generateSemesterName($order)
{
    $semesters = [
        '1' => '1st Semester',
        '2' => '2nd Semester',
        '3' => '3rd Semester',
        '4' => '4th Semester',
        '5' => '5th Semester',
        '6' => '6th Semester',
        '7' => '7th Semester',
        '8' => '8th Semester',
        '9' => '9th Semester',
        '10' => '10th Semester',
        '11' => '11th Semester',
        '12' => '12th Semester',
    ];

    return $semesters[$order] ?? 'Semester ' . $order;
}
