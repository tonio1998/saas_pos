<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Residents;
use App\Models\Teachers;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $data = [];
    public function index()
    {
        $students = Residents::count();
        $teachers = Teachers::count();
        $parents = Parents::count();

        $this->data = [
            'residents' => $students,
            'teachers' => $teachers,
            'parents' => $parents
        ];
        return view('pages.dashboard.index', $this->data);
    }
}
