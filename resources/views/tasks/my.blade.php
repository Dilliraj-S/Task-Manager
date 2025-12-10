@extends('layouts.app')
@section('title')
    My Tasks
@endsection

@section('content')
    <style>
        .kanban-column {
            background: #0f172a;
            padding: 10px;
            border-radius: 10px;
            height: 100%;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 12px 30px rgba(0,0,0,0.35);
        }

        .kanban-list {
            min-height: 500px;
            background: #111827;
            border-radius: 10px;
            padding: 10px;
            border: 1px dashed rgba(255,255,255,0.08);
        }

        .kanban-item {
            cursor: move;
            background: #1f2937;
            color: #e5e7eb;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .kanban-item .card-body {
            background: transparent;
        }

        .kanban-item.invisible {
            opacity: 0.4;
        }

        .empty-state {
            color: #cbd5e1;
        }
    </style>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2 class="text-center m-0">My Tasks</h2>
            <a href="{{ route('projects.index') }}" class="btn btn-primary">+ Add Task (via Project)</a>
        </div>

        <div class="row">
            @foreach (['to_do' => 'To Do', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $key => $label)
                <div class="col-md-4">
                    <div class="kanban-column mb-4">
                        <div class="d-flex justify-content-between bg-primary text-white shadow-sm align-items-center px-3 py-2 rounded-top">
                            <h4 class="text-white fw-bolder m-0">{{ $label }}</h4>
                        </div>
                        <div class="kanban-list" id="{{ $key }}">
                            @forelse ($tasks[$key] ?? [] as $task)
                                <div class="card mb-3 kanban-item" data-id="{{ $task->id }}" draggable="true">
                                    <div class="card-body">
                                        <h5 class="card-title mb-1">
                                            {{ $task->title }}
                                            <span style="font-size: 12px;"
                                                class="badge {{ $task->priority == 'low' ? 'bg-success' : ($task->priority == 'medium' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </h5>
                                        <p class="card-text mb-1 text-muted" style="font-size: 12px;">
                                            {{ optional($task->project)->name }}
                                        </p>
                                        <p class="card-text">{{ $task->description }}</p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <select class="form-select form-select-sm w-auto task-status-select" data-id="{{ $task->id }}">
                                                <option value="to_do" {{ $task->status === 'to_do' ? 'selected' : '' }}>To Do</option>
                                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted px-2 py-3 empty-state">No tasks in this status.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const kanbanItems = document.querySelectorAll('.kanban-item');
            const kanbanLists = document.querySelectorAll('.kanban-list');
            const statusSelects = document.querySelectorAll('.task-status-select');
            const csrf = '{{ csrf_token() }}';

            kanbanItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            kanbanLists.forEach(list => {
                list.addEventListener('dragover', handleDragOver);
                list.addEventListener('drop', handleDrop);
            });

            statusSelects.forEach(select => {
                select.addEventListener('change', (e) => {
                    const id = e.target.dataset.id;
                    const status = e.target.value;
                    updateTaskStatus(id, status);
                    moveCard(id, status);
                });
            });

            function handleDragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.dataset.id);
                setTimeout(() => {
                    e.target.classList.add('invisible');
                }, 0);
            }

            function handleDragEnd(e) {
                e.target.classList.remove('invisible');
            }

            function handleDragOver(e) {
                e.preventDefault();
            }

            function handleDrop(e) {
                e.preventDefault();
                const id = e.dataTransfer.getData('text');
                const draggableElement = document.querySelector(`.kanban-item[data-id='${id}']`);
                const dropzone = e.target.closest('.kanban-list');
                if (!dropzone) return;
                const sourceList = draggableElement.closest('.kanban-list');
                dropzone.appendChild(draggableElement);
                const status = dropzone.id;
                updateSelect(id, status);
                ensureEmptyState(dropzone);
                if (sourceList) ensureEmptyState(sourceList);
                updateTaskStatus(id, status);
            }

            function updateSelect(id, status) {
                const select = document.querySelector(`.task-status-select[data-id='${id}']`);
                if (select) select.value = status;
            }

            function moveCard(id, status) {
                const card = document.querySelector(`.kanban-item[data-id='${id}']`);
                const list = document.getElementById(status);
                if (card && list) {
                    const sourceList = card.closest('.kanban-list');
                    list.appendChild(card);
                    ensureEmptyState(list);
                    if (sourceList) ensureEmptyState(sourceList);
                }
            }

            function updateTaskStatus(id, status) {
                fetch(`/tasks/${id}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ status })
                }).then(response => {
                    if (!response.ok) throw new Error('Failed to update task status');
                    return response.json();
                }).catch(error => {
                    console.error(error);
                });
            }

            function ensureEmptyState(list) {
                const empty = list.querySelector('.empty-state');
                const hasCards = list.querySelectorAll('.kanban-item').length > 0;
                if (hasCards && empty) {
                    empty.remove();
                } else if (!hasCards && !empty) {
                    const div = document.createElement('div');
                    div.className = 'text-muted px-2 py-3 empty-state';
                    div.textContent = 'No tasks in this status.';
                    list.appendChild(div);
                }
            }

            // Initialize empty states on load
            kanbanLists.forEach(list => ensureEmptyState(list));
        });
    </script>
@endsection

