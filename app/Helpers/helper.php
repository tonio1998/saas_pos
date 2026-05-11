<?php


use App\Models\QrCodes;
use App\Models\SmsQueuingModel;

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

function generateQrCode()
{
    $prefix = env('SCHOOL_ID');

    $lastRow = QrCodes::where('prefix', 1)
        ->orderBy('last_number', 'desc')
        ->first();

    $newNumber = ($lastRow->last_number ?? 0) + 1;

    $qrCodeRow = new QrCodes();
    $qrCodeRow->prefix = $prefix;
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
