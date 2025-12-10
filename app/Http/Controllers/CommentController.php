<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'comment' => 'required|string|max:5000',
            ]);

            $comment = $task->comments()->create([
                'user_id' => Auth::id(),
                'comment' => $validated['comment'],
            ]);

            $comment->load('user');

            // Send notification to task assignee if commented by someone else
            if ($task->user_id != Auth::id()) {
                $assignedUser = $task->user;
                if ($assignedUser) {
                    \App\Services\NotificationService::notifyTaskComment($assignedUser, $task, $comment, Auth::user());
                }
            }

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment added successfully.',
                    'comment' => [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'user_name' => $comment->user->name,
                        'user_id' => $comment->user_id,
                        'created_at' => $comment->created_at->diffForHumans(),
                        'created_at_full' => $comment->created_at->format('M d, Y h:i A'),
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Comment added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully.',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at->diffForHumans(),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        if (request()->ajax() || request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
