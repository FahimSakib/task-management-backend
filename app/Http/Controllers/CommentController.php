<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
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

        $comment = Comment::create($validated);

        // return $comment->user;

        $task = Task::with(['users', 'comments.user'])->find($request->task_id);

        return response()->json(['success' => true, 'msg' => 'Comment added successfully', 'data' => $task]);
    }
}
