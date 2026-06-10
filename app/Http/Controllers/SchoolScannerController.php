<?php

namespace App\Http\Controllers;

use App\Models\SmsQueuingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class SchoolScannerController extends Controller
{
    public function index(){
        return view('pages.store.scanner.index');
    }
}
