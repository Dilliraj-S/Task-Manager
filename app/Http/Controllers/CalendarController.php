<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Routine;
use App\Models\Reminder;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', now()->format('Y-m'));
        $view = $request->get('view', 'month'); // month, week, day

        // Parse the date
        $currentDate = Carbon::parse($date . '-01');
        $startDate = $currentDate->copy()->startOfMonth();
        $endDate = $currentDate->copy()->endOfMonth();

        // Adjust dates based on view
        if ($view === 'week') {
            $weekDate = $request->get('week', now()->format('Y-m-d'));
            $currentDate = Carbon::parse($weekDate);
            $startDate = $currentDate->copy()->startOfWeek();
            $endDate = $currentDate->copy()->endOfWeek();
        } elseif ($view === 'day') {
            $dayDate = $request->get('day', now()->format('Y-m-d'));
            $currentDate = Carbon::parse($dayDate);
            $startDate = $currentDate->copy()->startOfDay();
            $endDate = $currentDate->copy()->endOfDay();
        }

        // Get user's accessible projects
        $projectIds = $this->getUserAccessibleProjectIds($user);

        // Fetch events
        $events = $this->getCalendarEvents($user, $projectIds, $startDate, $endDate);

        return view('calendar.index', compact(
            'events',
            'currentDate',
            'startDate',
            'endDate',
            'view'
        ));
    }

    public function events(Request $request)
    {
        $user = Auth::user();
        $start = Carbon::parse($request->get('start'));
        $end = Carbon::parse($request->get('end'));

        $projectIds = $this->getUserAccessibleProjectIds($user);
        $events = $this->getCalendarEvents($user, $projectIds, $start, $end);

        return response()->json($events);
    }

    private function getUserAccessibleProjectIds($user)
    {
        if ($user->isAdmin()) {
            return Project::pluck('id')->toArray();
        }

        // Get owned projects + team projects
        $ownedProjects = $user->projects()->pluck('projects.id');
        $teamProjects = $user->projectMembers()->pluck('projects.id');

        return $ownedProjects->merge($teamProjects)->unique()->toArray();
    }

    private function getCalendarEvents($user, $projectIds, $startDate, $endDate)
    {
        $events = [];

        // 1. Tasks with due dates
        $tasks = Task::whereIn('project_id', $projectIds)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['project', 'user'])
            ->get();

        foreach ($tasks as $task) {
            $events[] = [
                'id' => 'task-' . $task->id,
                'title' => $task->title,
                'start' => $task->due_date,
                'type' => 'task',
                'status' => $task->status,
                'priority' => $task->priority,
                'project' => $task->project->name,
                'assignee' => $task->user->name,
                'url' => route('tasks.show', $task->id),
                'backgroundColor' => $this->getTaskColor($task),
                'borderColor' => $this->getTaskColor($task),
                'textColor' => '#fff'
            ];
        }

        // 2. User's personal reminders
        $reminders = Reminder::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        foreach ($reminders as $reminder) {
            $events[] = [
                'id' => 'reminder-' . $reminder->id,
                'title' => '🔔 ' . $reminder->title,
                'start' => $reminder->date . ($reminder->time ? ' ' . $reminder->time : ''),
                'type' => 'reminder',
                'description' => $reminder->description,
                'url' => route('reminders.edit', $reminder->id),
                'backgroundColor' => '#dc3545',
                'borderColor' => '#dc3545',
                'textColor' => '#fff'
            ];
        }

        // 3. User's routines
        $routines = Routine::where('user_id', $user->id)->get();

        foreach ($routines as $routine) {
            $routineEvents = $this->generateRoutineEvents($routine, $startDate, $endDate);
            $events = array_merge($events, $routineEvents);
        }

        // 4. Project milestones (start/end dates)
        $projects = Project::whereIn('id', $projectIds)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->get();

        foreach ($projects as $project) {
            if ($project->start_date && $project->start_date->between($startDate, $endDate)) {
                $events[] = [
                    'id' => 'project-start-' . $project->id,
                    'title' => '🚀 ' . $project->name . ' (Start)',
                    'start' => $project->start_date->format('Y-m-d'),
                    'type' => 'project_start',
                    'project' => $project->name,
                    'url' => route('projects.show', $project->id),
                    'backgroundColor' => '#28a745',
                    'borderColor' => '#28a745',
                    'textColor' => '#fff'
                ];
            }

            if ($project->end_date && $project->end_date->between($startDate, $endDate)) {
                $events[] = [
                    'id' => 'project-end-' . $project->id,
                    'title' => '🎯 ' . $project->name . ' (Deadline)',
                    'start' => $project->end_date->format('Y-m-d'),
                    'type' => 'project_end',
                    'project' => $project->name,
                    'url' => route('projects.show', $project->id),
                    'backgroundColor' => '#fd7e14',
                    'borderColor' => '#fd7e14',
                    'textColor' => '#fff'
                ];
            }
        }

        return $events;
    }

    private function getTaskColor($task)
    {
        // Color by priority
        $priorityColors = [
            'high' => '#dc3545',
            'medium' => '#ffc107',
            'low' => '#6c757d'
        ];

        return $priorityColors[$task->priority] ?? '#6c757d';
    }

    private function generateRoutineEvents($routine, $startDate, $endDate)
    {
        $events = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            if ($this->shouldIncludeRoutine($routine, $currentDate)) {
                $events[] = [
                    'id' => 'routine-' . $routine->id . '-' . $currentDate->format('Y-m-d'),
                    'title' => '🔄 ' . $routine->title,
                    'start' => $currentDate->format('Y-m-d') . ' ' . $routine->start_time,
                    'end' => $currentDate->format('Y-m-d') . ' ' . $routine->end_time,
                    'type' => 'routine',
                    'frequency' => $routine->frequency,
                    'description' => $routine->description,
                    'url' => route('routines.edit', $routine->id),
                    'backgroundColor' => '#6f42c1',
                    'borderColor' => '#6f42c1',
                    'textColor' => '#fff'
                ];
            }
            $currentDate->addDay();
        }

        return $events;
    }

    private function shouldIncludeRoutine($routine, $date)
    {
        switch ($routine->frequency) {
            case 'daily':
                return true;

            case 'weekly':
                $days = json_decode($routine->days, true) ?? [];
                return in_array($date->dayOfWeek, $days);

            case 'monthly':
                $days = json_decode($routine->days, true) ?? [];
                return in_array($date->day, $days);

            default:
                return false;
        }
    }
}