<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required',
            'description' => 'required'
        ]);

        $validated['created_by'] = $request->user()->id;
        $result                  = Task::create($validated);

        if ($result) {
            return response()->json(['success' => true, 'msg' => 'Task created successfully']);
        }

        return response()->json(['success' => false, 'msg' => 'Someting went wrong']);
    }
}
