<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Models\Students;
use App\Models\Teachers;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $data = [];
    public function index()
    {
        $teachers = Teachers::count();
        $parents = Parents::count();
        $students = Students::count();

        $this->data = [
            'teachers' => $teachers,
            'parents' => $parents,
            'students' => $students
        ];
        return view('pages.dashboard.index', $this->data);
    }
}
