<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Reminder;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickAddController extends Controller
{
    public function quickTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id'
        ]);

        $user = Auth::user();
        
        // If no project specified, try to find user's default project or create one
        $projectId = $request->project_id;
        if (!$projectId) {
            $defaultProject = $user->projects()->where('name', 'Quick Tasks')->first();
            
            if (!$defaultProject) {
                $defaultProject = Project::create([
                    'user_id' => $user->id,
                    'name' => 'Quick Tasks',
                    'description' => 'Auto-created project for quick tasks from calendar',
                    'status' => 'in_progress'
                ]);
            }
            
            $projectId = $defaultProject->id;
        }

        $task = Task::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'title' => $request->title,
            'due_date' => $request->due_date,
            'priority' => 'medium',
            'status' => 'to_do'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully!',
            'task' => $task
        ]);
    }

    public function quickReminder(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable|string'
        ]);

        $reminder = Reminder::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description ?? 'Quick reminder from calendar',
            'date' => $request->date,
            'time' => $request->time
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder created successfully!',
            'reminder' => $reminder
        ]);
    }
}