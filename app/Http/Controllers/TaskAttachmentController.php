<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        try {
            $validated = $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'name' => 'nullable|string|max:255',
            ]);

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $name = $request->input('name') ?: pathinfo($originalName, PATHINFO_FILENAME);
            
            $path = $file->store('task-attachments', 'public');

            $attachment = $task->attachments()->create([
                'user_id' => Auth::id(),
                'name' => $name,
                'file_name' => $originalName,
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $attachment->load('user');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully.',
                    'attachment' => [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'file_name' => $attachment->file_name,
                        'file_size_human' => $attachment->file_size_human,
                        'mime_type' => $attachment->mime_type,
                        'is_image' => $attachment->isImage(),
                        'is_pdf' => $attachment->isPdf(),
                        'file_url' => Storage::url($attachment->file_path),
                        'user_name' => $attachment->user->name,
                        'created_at' => $attachment->created_at->diffForHumans(),
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'File uploaded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }

    public function destroy(TaskAttachment $taskAttachment)
    {
        try {
            // Check if user has access to the task
            $task = $taskAttachment->task;
            $project = $task->project;
            
            $hasAccess = $project->user_id === Auth::id() || 
                         $project->users()->where('user_id', Auth::id())->exists() ||
                         $taskAttachment->user_id === Auth::id();

            if (!$hasAccess) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized action.',
                    ], 403);
                }
                abort(403, 'Unauthorized action.');
            }

            Storage::disk('public')->delete($taskAttachment->file_path);
            $taskAttachment->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully.',
                ]);
            }

            return redirect()->back()->with('success', 'File deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }

    public function download(TaskAttachment $taskAttachment)
    {
        $task = $taskAttachment->task;
        $project = $task->project;
        
        $hasAccess = $project->user_id === Auth::id() || 
                     $project->users()->where('user_id', Auth::id())->exists();

        abort_unless($hasAccess, 403, 'Unauthorized action.');

        if (!Storage::disk('public')->exists($taskAttachment->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $taskAttachment->file_path,
            $taskAttachment->file_name
        );
    }
}
