<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        switch (config('app.salon')) {
            // 中田敦彦オンラインサロン
            case 'progress':
                return view('welcome-progress');

            // 西野亮廣エンタメ研究所
            case 'nishino':
                return view('welcome');
        }

        return view('welcome');
    }
}
