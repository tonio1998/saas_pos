<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Students;
use App\Models\Employees;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $data = [];
    public function index()
    {
        $teachers = Employees::count();
        $parents = Parents::count();
        $students = Students::count();

        $this->data = [
            'employees' => $teachers,
            'parents' => $parents,
            'students' => $students
        ];
        return view('pages.dashboard.index', $this->data);
    }
}
