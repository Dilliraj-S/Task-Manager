<?php
namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\Task;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'task_id' => 'required|exists:tasks,id',
        ]);

        $task = Task::findOrFail($request->task_id);
        $this->authorizeTask($task);

        $checklistItem = ChecklistItem::create([
            'task_id' => $request->task_id,
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'data' =>  $checklistItem
            
        ]);
    }

    public function updateStatus(ChecklistItem $checklistItem)
    {
        $this->authorizeChecklistItem($checklistItem);
        $checklistItem->update([
            'completed' => !$checklistItem->completed, 
        ]);
        return response()->json([
            'success' => true,
            'status' => 200
        ]);
    }

    public function update(Request $request, ChecklistItem $checklistItem)
    {
        $this->authorizeChecklistItem($checklistItem);
        $checklistItem->update([
            'completed' => $request->has('completed'),
            'name' => $request->name,
        ]);
        return back()->with('success', 'Checklist item updated successfully.');
    }

    public function destroy(ChecklistItem $checklistItem)
    {
        $this->authorizeChecklistItem($checklistItem);
        $checklistItem->delete();
        return response()->json([
            'success' => true,
        ]);
    }

    private function authorizeChecklistItem(ChecklistItem $checklistItem): void
    {
        $this->authorizeTask($checklistItem->task);
    }

    private function authorizeTask(Task $task): void
    {
        abort_unless($task->project->isAccessibleBy(auth()->user()), 403);
    }
}
