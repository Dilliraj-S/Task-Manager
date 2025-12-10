<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get projects owned by user
        $ownedProjects = $user->projects()->withCount(['tasks as to_do_tasks' => function ($query) {
            $query->where('status', 'to_do');
        }, 'tasks as in_progress_tasks' => function ($query) {
            $query->where('status', 'in_progress');
        }, 'tasks as completed_tasks' => function ($query) {
            $query->where('status', 'completed');
        }])->get();

        // Get projects where user is a team member
        $teamProjects = $user->projectMembers()->withCount(['tasks as to_do_tasks' => function ($query) {
            $query->where('status', 'to_do');
        }, 'tasks as in_progress_tasks' => function ($query) {
            $query->where('status', 'in_progress');
        }, 'tasks as completed_tasks' => function ($query) {
            $query->where('status', 'completed');
        }])->get();

        // Merge both collections and remove duplicates
        $projects = $ownedProjects->merge($teamProjects)->unique('id');

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:not_started,in_progress,completed',
            'budget' => 'nullable|numeric',
        ]);

        Auth::user()->projects()->create($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'status',
            'budget',
        ]));

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->authorizeProjectAccess($project);
        $teamMembers = $project->users()->get();
        $users = User::all();
        return view('projects.show', compact('project', 'teamMembers', 'users'));
    }
    public function edit(Project $project)
    {
        $this->authorizeProjectOwner($project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeProjectOwner($project);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:not_started,in_progress,completed',
            'budget' => 'nullable|numeric',
        ]);

        $project->update($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'status',
            'budget',
        ]));

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeProjectOwner($project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);
       
        $project = Project::findOrFail($request->project_id);
        $this->authorizeProjectOwner($project);

        $project->teamProjects()->syncWithoutDetaching([$request->user_id]);
        
        // Send notification to added user
        $addedUser = User::find($request->user_id);
        if ($addedUser && $addedUser->id != Auth::id()) {
            \App\Services\NotificationService::notifyProjectInvite($addedUser, $project, Auth::user());
        }
        
        return redirect()->back()->with('success', 'User added successfully.');
    }

    public function removeMember(Project $project, User $user)
    {
        $this->authorizeProjectOwnerOrAdmin($project);

        // prevent removing owner
        abort_if($project->user_id === $user->id, 403, 'Cannot remove project owner.');

        $project->users()->detach($user->id);

        return redirect()->back()->with('success', 'Member removed successfully.');
    }

    private function authorizeProjectAccess(Project $project): void
    {
        abort_unless($project->isAccessibleBy(Auth::user()), 403, 'You do not have access to this project.');
    }

    private function authorizeProjectOwner(Project $project): void
    {
        abort_unless($project->user_id === Auth::id(), 403, 'Only the project owner can perform this action.');
    }

    private function authorizeProjectOwnerOrAdmin(Project $project): void
    {
        abort_unless($project->user_id === Auth::id() || Auth::user()?->isAdmin(), 403, 'Only the project owner or an admin can perform this action.');
    }
}
