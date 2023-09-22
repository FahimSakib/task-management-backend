<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Notifications\sendTaskNotification;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('users')->get();
        $loggedUserId = auth()->user()->id;

        return response()->json(['tasks' => $tasks, 'loggedUserId' => $loggedUserId]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required',
            'description' => 'required'
        ]);

        $validated['created_by'] = $request->user()->id;
        $task                  = Task::create($validated);

        if ($task) {
            if (!empty($request->assignedUsers)) {
                $assignedUsers = explode(',', $request->assignedUsers);
                $task->users()->sync($assignedUsers);
                $this->sendNotification($task);
            }

            return response()->json(['success' => true, 'msg' => 'Task created successfully']);
        }

        return response()->json(['success' => false, 'msg' => 'Someting went wrong']);
    }

    public function view($id)
    {
        $task = Task::with('users')->find($id);
        return response()->json($task);
    }

    protected function sendNotification($task)
    {
        $users = $task->users;

        foreach ($users as $user) {
            $user->notify(new sendTaskNotification($task));
        }
    }
}
