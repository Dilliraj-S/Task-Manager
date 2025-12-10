<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Project $project)
    {
        $this->authorizeProjectAccess($project);
        $tasks = $project->tasks()->get()->groupBy('status');
        $users = $project->users()->get();
        return view('tasks.index', compact('project', 'tasks', 'users'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProjectAccess($project);
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to_do,in_progress,completed',
        ]);

        $validated = $request->only([
            'user_id',
            'title',
            'description',
            'due_date',
            'priority',
            'status',
        ]);

        $this->ensureUserOnProject($project, $validated['user_id']);

        $task = $project->tasks()->create($validated + ['project_id' => $project->id]);

        // Send notification to assigned user if not assigning to self
        if ($validated['user_id'] != Auth::id()) {
            $assignedUser = \App\Models\User::find($validated['user_id']);
            if ($assignedUser) {
                \App\Services\NotificationService::notifyTaskAssigned($assignedUser, $task, Auth::user());
            }
        }

        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorizeProjectAccess($task->project);
        $task->load(['comments.user', 'checklistItems', 'attachments.user']);
        return view('tasks.show', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeProjectAccess($task->project);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to_do,in_progress,completed',
        ]);

        $validated = $request->only([
            'title',
            'description',
            'due_date',
            'priority',
            'status',
        ]);

        $task->update($validated);

        // Send notification to task assignee if updated by someone else
        if ($task->user_id != Auth::id()) {
            $assignedUser = $task->user;
            if ($assignedUser) {
                \App\Services\NotificationService::notifyTaskUpdated($assignedUser, $task, Auth::user());
            }
        }

        return redirect()->route('projects.tasks.index', $task->project_id)->with('success', 'Task updated successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeProjectAccess($task->project);
        $request->validate([
            'status' => 'required|in:to_do,in_progress,completed',
        ]);

        $task->status = $request->input('status');
        $task->save();

        return response()->json(['message' => 'Task status updated successfully.']);
    }

    public function myTasks()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tasks = Task::with('project')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id) // assigned to me
                    ->orWhereHas('project', function ($q) use ($user) {
                        $q->where('user_id', $user->id) // I own the project
                            ->orWhereHas('users', function ($q2) use ($user) {
                                $q2->where('users.id', $user->id); // I'm on the team
                            });
                    });
            })
            ->get()
            ->groupBy('status');
        return view('tasks.my', compact('tasks'));
    }

    private function authorizeProjectAccess(Project $project): void
    {
        abort_unless($project->isAccessibleBy(Auth::user()), 403);
    }

    private function ensureUserOnProject(Project $project, int $userId): void
    {
        $isOwner = $project->user_id === $userId;
        $isMember = $project->users()->whereKey($userId)->exists();
        abort_unless($isOwner || $isMember, 422, 'Assignee must be a project member.');
    }
}
