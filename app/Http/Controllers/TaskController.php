<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
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
                $this->sendNotification($task->users, $task);
            }

            return response()->json(['success' => true, 'msg' => 'Task created successfully']);
        }

        return response()->json(['success' => false, 'msg' => 'Someting went wrong']);
    }

    public function view($id)
    {
        $currentUserId = auth()->user()->id;
        $task = Task::with(['users', 'comments.user'])->find($id);

        return response()->json(['task' => $task, 'currentUserId' => $currentUserId]);
    }

    public function edit($id)
    {
        $currentUser = auth()->user();
        $task        = Task::with('users')->find($id);

        if ($task->users->contains($currentUser) || $task->created_by == $currentUser->id) {
            $users = User::all();
            return response()->json(['users' => $users, 'task' => $task]);
        }

        return response()->json(['success' => false, 'msg' => 'Access denied']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required',
            'description' => 'required'
        ]);

        $task = Task::find($id);

        if (!empty($request->assignedUsers)) {
            $assignedUsers        = explode(',', $request->assignedUsers);
            $currentAssignedUsers = $task->users;
            $newlyAssignedUsers   = User::whereIn('id', $assignedUsers)->get();
            $onlyNewUsers         = $newlyAssignedUsers->diff($currentAssignedUsers);
        }

        $result = $task->update($validated);

        if ($result) {
            if (!empty($assignedUsers)) {
                $task->users()->sync($assignedUsers);
                $this->sendNotification($onlyNewUsers, $task);
            } else {
                $task->users()->sync([]);
            }

            return response()->json(['success' => true, 'msg' => 'Task updated successfully']);
        }

        return response()->json(['success' => false, 'msg' => 'Someting went wrong']);
    }

    public function delete($id)
    {
        $result = Task::find($id)->delete();

        if ($result) {
            return response()->json(['success' => true, 'msg' => 'Task deleted successfully']);
        }

        return response()->json(['success' => false, 'msg' => 'Someting went wrong']);
    }

    protected function sendNotification($users, $task)
    {
        foreach ($users as $user) {
            $user->notify(new sendTaskNotification($task));
        }
    }
}
