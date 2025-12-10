<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function create($userId, $type, $title, $message, $link = null, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data,
        ]);
    }

    public static function notifyTaskAssigned(User $user, $task, $assignedBy)
    {
        return self::create(
            $user->id,
            'task_assigned',
            'New Task Assigned',
            "You have been assigned to task: {$task->title}",
            route('tasks.show', $task->id),
            [
                'task_id' => $task->id,
                'assigned_by' => $assignedBy->name,
            ]
        );
    }

    public static function notifyTaskComment(User $user, $task, $comment, $commentedBy)
    {
        return self::create(
            $user->id,
            'task_comment',
            'New Comment on Task',
            "{$commentedBy->name} commented on task: {$task->title}",
            route('tasks.show', $task->id),
            [
                'task_id' => $task->id,
                'comment_id' => $comment->id,
                'commented_by' => $commentedBy->name,
            ]
        );
    }

    public static function notifyTaskUpdated(User $user, $task, $updatedBy)
    {
        return self::create(
            $user->id,
            'task_updated',
            'Task Updated',
            "Task '{$task->title}' has been updated by {$updatedBy->name}",
            route('tasks.show', $task->id),
            [
                'task_id' => $task->id,
                'updated_by' => $updatedBy->name,
            ]
        );
    }

    public static function notifyProjectInvite(User $user, $project, $invitedBy)
    {
        return self::create(
            $user->id,
            'project_invite',
            'Project Invitation',
            "You have been added to project: {$project->name}",
            route('projects.show', $project->id),
            [
                'project_id' => $project->id,
                'invited_by' => $invitedBy->name,
            ]
        );
    }
}

