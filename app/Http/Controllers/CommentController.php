<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\sendCommentNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required',
            'user_id' => 'required',
            'task_id' => 'required',
        ]);

        $comment       = Comment::create($validated);
        $task          = Task::with(['users', 'comments.user'])->find($request->task_id);
        $currentUserId = auth()->user()->id;
        $taskCreator   = User::find($task->created_by);

        foreach ($task->users as $user) {
            if ($user->id != $currentUserId) {
                $user->notify(new sendCommentNotification($comment, $task));
            }
        }

        if (!$task->users->contains($taskCreator) && $taskCreator->id != $currentUserId) {
            $taskCreator->notify(new sendCommentNotification($comment, $task));
        }

        return response()->json(['success' => true, 'msg' => 'Comment added successfully', 'data' => $task]);
    }
}
