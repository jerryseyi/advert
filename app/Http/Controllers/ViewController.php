<?php

namespace App\Http\Controllers;

use App\Models\View;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function index()
    {
        return View::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'device_id' => ['required', 'integer'],
            'user_id' => ['required'],
            'count' => ['required'],
        ]);

        return View::create($data);
    }

    public function show(View $view)
    {
        return $view;
    }

    public function update(Request $request, View $view)
    {
        $data = $request->validate([
            'device_id' => ['required', 'integer'],
            'user_id' => ['required'],
            'count' => ['required'],
        ]);

        $view->update($data);

        return $view;
    }

    public function destroy(View $view)
    {
        $view->delete();

        return response()->json();
    }
}
