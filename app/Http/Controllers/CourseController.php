<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    public function index() {
        $courses = Course::all();

        return response()->json($courses);
    }
}
