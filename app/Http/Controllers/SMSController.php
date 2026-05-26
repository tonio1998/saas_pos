<?php

namespace App\Http\Controllers;

use App\Models\SmsQueuingModel;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.sms.index');
    }

    public function ajaxData(Request $request)
    {
        $query = SmsQueuingModel::query()
        ->orderBy('created_at', 'desc');

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($sms) {

                return '';
            })
            ->addColumn('phone_number', function ($sms) {
                return $sms->PhoneNumber;
            })
            ->addColumn('message', function ($sms) {
                return $sms->Message;
            })
            ->addColumn('status', function ($sms) {
                return $sms->remark;
            })
            ->addColumn('created_at', function ($sms) {
                return date('M d, Y h:i A', strtotime($sms->created_at));
            })
            ->addColumn('createdBy', function ($sms) {
                return $sms->createdBy?->name ?? '';
            })
            ->filterColumn('message', function ($query, $keyword) {
                $query->where('Message', 'like', "%{$keyword}%");
            })
            ->rawColumns(['actions','phone_number','message','status','created_at','createdBy'])
            ->make(true);
    }
}
