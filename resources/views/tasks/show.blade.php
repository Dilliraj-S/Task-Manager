@extends('layouts.app')
@section('title')
    {{ $task->title }} - Task Details
@endsection
@section('content')
    <div class="container">
        <h2 class="mb-4 shadow-sm p-3 rounded bg-white text-center">{{ $task->title }} - Task Details</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <p class="card-text"><strong>Due Date:</strong> {{ $task->due_date }}</p>
                                <p class="card-text"><strong>Priority:</strong> <span
                                        class="badge {{ $task->priority == 'low' ? 'bg-success' : ($task->priority == 'medium' ? 'bg-warning' : 'bg-danger') }}">{{ ucfirst($task->priority) }}</span>
                                </p>
                                <p class="card-text"><strong>Status:</strong>
                                    @if ($task->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($task->status == 'to_do')
                                        <span class="badge bg-primary">To Do</span>
                                    @elseif($task->status == 'in_progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @endif
                                </p>

                                <p class="card-text"><strong>Assign To:</strong> {{ $task->user->name }}</p>

                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editTaskModal"> <i class="bi bi-pencil-square"></i> </button>
                                <a href="{{ route('projects.tasks.index', $task->project->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-90deg-left"></i> </a>
                            </div>

                            
                            <div class="col-md-12 mt-3">
                                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                    <h5>Checklist</h5>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addChecklistModal"> <i class="bi bi-plus-circle"></i> </button>
                                </div>

                                <!-- Checklist items -->
                                <ul class="list-group mt-2" id="checklist-items">
                                    @foreach ($task->checklistItems as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center"
                                            id="checklist-item-{{ $item->id }}">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="checklist-item-checkbox-{{ $item->id }}"
                                                    {{ $item->completed ? 'checked' : '' }}
                                                    onchange="toggleChecklistItem({{ $item->id }})">
                                                <label
                                                    class="form-check-label {{ $item->completed ? 'text-decoration-line-through' : '' }}">{{ $item->name }}</label>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editChecklistModal-{{ $item->id }}"><i
                                                        class="bi bi-pencil-square"></i></button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="deleteChecklistItem({{ $item->id }})"><i
                                                        class="bi bi-trash"></i></button>
                                            </div>
                                        </li>

                                        <!-- Edit Checklist Modal -->
                                    @endforeach
                                </ul>
                            </div>
                            
                            <!-- Attachments Section -->
                            <div class="col-md-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                    <h5><i class="bi bi-paperclip"></i> Attachments</h5>
                                    <span class="badge bg-info">{{ $task->attachments->count() }}</span>
                                </div>
                                
                                <!-- Upload File Form -->
                                <div class="mt-3 mb-4">
                                    <form id="upload-attachment-form" enctype="multipart/form-data">
                                                    @csrf
                                        <div class="input-group">
                                            <input type="file" name="file" id="attachment-file" class="form-control" required>
                                            <input type="text" name="name" id="attachment-name" class="form-control" 
                                                placeholder="Optional: Custom name" style="max-width: 200px;">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-upload"></i> Upload
                                            </button>
                                        </div>
                                        <small class="text-muted">Max file size: 10MB</small>
                                    </form>
                                </div>
                                
                                <!-- Attachments List -->
                                <div id="attachments-list" class="row g-2">
                                    @forelse($task->attachments as $attachment)
                                        <div class="col-md-6 col-lg-4 attachment-item" id="attachment-{{ $attachment->id }}">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <div class="d-flex align-items-start">
                                                        <div class="flex-shrink-0 me-2">
                                                            @if($attachment->isImage())
                                                                <img src="{{ Storage::url($attachment->file_path) }}" 
                                                                    alt="{{ $attachment->name }}" 
                                                                    class="img-thumbnail" 
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            @elseif($attachment->isPdf())
                                                                <div class="bg-danger text-white d-flex align-items-center justify-content-center" 
                                                                    style="width: 50px; height: 50px; border-radius: 4px;">
                                                                    <i class="bi bi-file-pdf fs-4"></i>
                                                                </div>
                                                            @else
                                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                                    style="width: 50px; height: 50px; border-radius: 4px;">
                                                                    <i class="bi bi-file-earmark fs-4"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1" style="min-width: 0;">
                                                            <h6 class="mb-0 text-truncate" title="{{ $attachment->name }}">
                                                                {{ $attachment->name }}
                                                            </h6>
                                                            <small class="text-muted d-block">{{ $attachment->file_size_human }}</small>
                                                            <small class="text-muted">{{ $attachment->created_at->diffForHumans() }}</small>
                                                    </div>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-link text-muted p-0" type="button" 
                                                                data-bs-toggle="dropdown">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('task-attachments.download', $attachment->id) }}" target="_blank">
                                                                        <i class="bi bi-download"></i> Download
                                                                    </a>
                                                                </li>
                                                                @if($attachment->user_id == Auth::id() || $task->project->user_id == Auth::id())
                                                                    <li>
                                                                        <a class="dropdown-item delete-attachment-btn text-danger" href="#" 
                                                                            data-attachment-id="{{ $attachment->id }}">
                                                                            <i class="bi bi-trash"></i> Delete
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center text-muted py-4">
                                                <i class="bi bi-paperclip fs-1"></i>
                                                <p class="mt-2">No attachments yet. Upload files to share with your team!</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            
                            <!-- Comments Section -->
                            <div class="col-md-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                    <h5><i class="bi bi-chat-dots"></i> Comments & Discussions</h5>
                                    <span class="badge bg-secondary">{{ $task->comments->count() }}</span>
                                </div>
                                
                                <!-- Add Comment Form -->
                                <div class="mt-3 mb-4">
                                    <form id="add-comment-form">
                                        @csrf
                                        <div class="mb-2">
                                            <textarea name="comment" id="comment-text" class="form-control" rows="3" 
                                                placeholder="Add a comment..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-send"></i> Post Comment
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Comments List -->
                                <div id="comments-list" class="mt-3">
                                    @forelse($task->comments as $comment)
                                        <div class="card mb-3 comment-item" id="comment-{{ $comment->id }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <strong class="me-2">{{ $comment->user->name }}</strong>
                                                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0 comment-content" id="comment-content-{{ $comment->id }}">
                                                            {{ $comment->comment }}
                                                        </p>
                                                    </div>
                                                    @if($comment->user_id == Auth::id())
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-link text-muted" type="button" 
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item edit-comment-btn" href="#" 
                                                                        data-comment-id="{{ $comment->id }}" 
                                                                        data-comment-text="{{ $comment->comment }}">
                                                                        <i class="bi bi-pencil"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item delete-comment-btn" href="#" 
                                                                        data-comment-id="{{ $comment->id }}">
                                                                        <i class="bi bi-trash"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            <i class="bi bi-chat-dots fs-1"></i>
                                            <p class="mt-2">No comments yet. Be the first to comment!</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Checklist Modal -->
        <div class="modal fade" id="addChecklistModal" tabindex="-1" aria-labelledby="addChecklistModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="add-checklist-form">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addChecklistModalLabel">Add Checklist Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="checklist-name" class="form-label">Item Name</label>
                                <input type="text" name="name" id="checklist-name" class="form-control" required>
                                <div class="invalid-feedback" id="checklist-name-error"></div>
                            </div>
                            <input type="hidden" name="task_id" id="task_id" value="{{ $task->id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ $task->title }}" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control">{{ $task->description }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                    value="{{ $task->due_date }}">
                                @error('due_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium
                                    </option>
                                    <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }}>To Do</option>
                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In
                                        Progress</option>
                                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

        function toggleChecklistItem(itemId) {
            const url = '{{ route('checklist-items.update-status', ':id') }}'.replace(':id', itemId);
            const checkbox = document.getElementById(`checklist-item-checkbox-${itemId}`);
            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const label = checkbox.closest('.form-check').querySelector('.form-check-label');
                        label.classList.toggle('text-decoration-line-through', checkbox.checked);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // function toggleChecklistItem(itemId) {
        //     const checkbox = document.getElementById(`checklist-item-checkbox-${itemId}`);
        //     const form = document.getElementById(`edit-checklist-form-${itemId}`);
        //     const formData = new FormData(form);
        //     formData.append('completed', checkbox.checked ? '1' : '0');

        //     fetch(form.action, {
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         },
        //         body: formData
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             const itemElement = checkbox.closest('li');
        //             const label = checkbox.nextElementSibling;
        //             label.classList.toggle('text-decoration-line-through', checkbox.checked);
        //         }
        //     })
        //     .catch(error => console.error('Error:', error));
        // }

        function deleteChecklistItem(itemId) {
            const form = document.getElementById(`delete-checklist-form-${itemId}`);
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`checklist-item-${itemId}`).remove();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // AJAX for adding checklist item
        document.getElementById('add-checklist-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            fetch('{{ route('checklist-items.store', $task->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(data)
                        const checklistItem = document.createElement('li');
                        checklistItem.className =
                            'list-group-item d-flex justify-content-between align-items-center';
                        checklistItem.id = `checklist-item-${data.id}`;
                        checklistItem.innerHTML = `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checklist-item-checkbox-${data.id}"
                                onchange="toggleChecklistItem(${data.id})">
                            <label class="form-check-label">${data.name}</label>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editChecklistModal-${data.id}"><i class="bi bi-pencil-square"></i></button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteChecklistItem(${data.id})"><i class="bi bi-trash"></i></button>
                        </div>
                    `;

                        document.getElementById('checklist-items').appendChild(checklistItem);
                        form.reset();
                        document.querySelector('#addChecklistModal .btn-close').click();
                    } else {
                        const errorElement = document.getElementById('checklist-name-error');
                        errorElement.textContent = data.message;
                        errorElement.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Comments functionality
        const addCommentForm = document.getElementById('add-comment-form');
        if (addCommentForm) {
            addCommentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const commentText = formData.get('comment').trim();
                
                if (!commentText) return;

                fetch('{{ route('comments.store', $task) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(errData.message || 'Server error: ' + response.status);
                        }).catch(() => {
                            throw new Error('Server error: ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const commentsList = document.getElementById('comments-list');
                        const emptyState = commentsList.querySelector('.text-center');
                        if (emptyState) emptyState.remove();

                        const commentDiv = document.createElement('div');
                        commentDiv.className = 'card mb-3 comment-item';
                        commentDiv.id = `comment-${data.comment.id}`;
                        commentDiv.innerHTML = `
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-2">{{ Auth::user()->name }}</strong>
                                            <small class="text-muted">${data.comment.created_at}</small>
                                        </div>
                                        <p class="mb-0 comment-content" id="comment-content-${data.comment.id}">
                                            ${data.comment.comment}
                                        </p>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted" type="button" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item edit-comment-btn" href="#" 
                                                    data-comment-id="${data.comment.id}" 
                                                    data-comment-text="${data.comment.comment}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item delete-comment-btn" href="#" 
                                                    data-comment-id="${data.comment.id}">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                        commentsList.insertBefore(commentDiv, commentsList.firstChild);
                        document.getElementById('comment-text').value = '';
                        
                        // Update comment count badge
                        const badge = document.querySelector('.border-top .badge');
                        if (badge) {
                            const currentCount = parseInt(badge.textContent) || 0;
                            badge.textContent = currentCount + 1;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add comment. Please try again.');
                });
            });
        }

        // Edit comment
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-comment-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.edit-comment-btn');
                const commentId = btn.dataset.commentId;
                const commentText = btn.dataset.commentText;
                const commentContent = document.getElementById(`comment-content-${commentId}`);
                
                const textarea = document.createElement('textarea');
                textarea.className = 'form-control';
                textarea.value = commentText;
                textarea.rows = 3;
                
                const saveBtn = document.createElement('button');
                saveBtn.className = 'btn btn-primary btn-sm mt-2';
                saveBtn.innerHTML = '<i class="bi bi-check"></i> Save';
                
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'btn btn-secondary btn-sm mt-2 ms-2';
                cancelBtn.innerHTML = '<i class="bi bi-x"></i> Cancel';
                
                const originalContent = commentContent.innerHTML;
                commentContent.innerHTML = '';
                commentContent.appendChild(textarea);
                commentContent.appendChild(saveBtn);
                commentContent.appendChild(cancelBtn);
                
                saveBtn.addEventListener('click', function() {
                    const newText = textarea.value.trim();
                    if (!newText) return;
                    
                    fetch(`/comments/${commentId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ comment: newText })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            commentContent.innerHTML = newText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        commentContent.innerHTML = originalContent;
                    });
                });
                
                cancelBtn.addEventListener('click', function() {
                    commentContent.innerHTML = originalContent;
                });
            }
        });

        // Delete comment
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-comment-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-comment-btn');
                const commentId = btn.dataset.commentId;
                
                if (!confirm('Are you sure you want to delete this comment?')) return;
                
                fetch(`/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`comment-${commentId}`).remove();
                        
                        // Update comment count badge
                        const badge = document.querySelector('.border-top .badge');
                        if (badge) {
                            const currentCount = parseInt(badge.textContent) || 0;
                            badge.textContent = Math.max(0, currentCount - 1);
                        }
                        
                        // Show empty state if no comments
                        const commentsList = document.getElementById('comments-list');
                        if (commentsList.children.length === 0) {
                            commentsList.innerHTML = `
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-chat-dots fs-1"></i>
                                    <p class="mt-2">No comments yet. Be the first to comment!</p>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete comment. Please try again.');
                });
            }
        });

        // File Attachments functionality
        const uploadAttachmentForm = document.getElementById('upload-attachment-form');
        if (uploadAttachmentForm) {
            uploadAttachmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const fileInput = document.getElementById('attachment-file');
                
                if (!fileInput.files.length) {
                    alert('Please select a file to upload.');
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';

                fetch('{{ route('task-attachments.store', $task->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (!response.ok) {
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return Promise.reject(new Error(`HTTP error! status: ${response.status}`));
                    }
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    }
                    return Promise.reject(new Error('Response is not JSON'));
                })
                .then(data => {
                    if (data.success) {
                        const attachmentsList = document.getElementById('attachments-list');
                        const emptyState = attachmentsList.querySelector('.text-center');
                        if (emptyState) emptyState.remove();

                        const attachmentDiv = document.createElement('div');
                        attachmentDiv.className = 'col-md-6 col-lg-4 attachment-item';
                        attachmentDiv.id = `attachment-${data.attachment.id}`;
                        
                        const iconHtml = data.attachment.is_image 
                            ? `<img src="${data.attachment.file_url}" alt="${data.attachment.name}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">`
                            : data.attachment.is_pdf
                            ? `<div class="bg-danger text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;"><i class="bi bi-file-pdf fs-4"></i></div>`
                            : `<div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 4px;"><i class="bi bi-file-earmark fs-4"></i></div>`;

                        attachmentDiv.innerHTML = `
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-2">
                                            ${iconHtml}
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <h6 class="mb-0 text-truncate" title="${data.attachment.name}">
                                                ${data.attachment.name}
                                            </h6>
                                            <small class="text-muted d-block">${data.attachment.file_size_human}</small>
                                            <small class="text-muted">${data.attachment.created_at}</small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted p-0" type="button" 
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="/task-attachments/${data.attachment.id}/download" target="_blank">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item delete-attachment-btn text-danger" href="#" 
                                                        data-attachment-id="${data.attachment.id}">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        attachmentsList.insertBefore(attachmentDiv, attachmentsList.firstChild);
                        
                        // Reset form
                        this.reset();
                        document.getElementById('attachment-name').value = '';
                        
                        // Update attachment count badge
                        const badge = document.querySelector('.border-top .badge.bg-info');
                        if (badge) {
                            const currentCount = parseInt(badge.textContent) || 0;
                            badge.textContent = currentCount + 1;
                        }
                    } else {
                        alert(data.message || 'Failed to upload file. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMsg = error.message || (error.errors ? Object.values(error.errors).flat().join(', ') : 'Failed to upload file. Please try again.');
                    alert(errorMsg);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }

        // Delete attachment
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-attachment-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-attachment-btn');
                const attachmentId = btn.dataset.attachmentId;
                
                if (!confirm('Are you sure you want to delete this attachment?')) return;
                
                fetch(`/task-attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`attachment-${attachmentId}`).remove();
                        
                        // Update attachment count badge
                        const badge = document.querySelector('.border-top .badge.bg-info');
                        if (badge) {
                            const currentCount = parseInt(badge.textContent) || 0;
                            badge.textContent = Math.max(0, currentCount - 1);
                        }
                        
                        // Show empty state if no attachments
                        const attachmentsList = document.getElementById('attachments-list');
                        if (attachmentsList && attachmentsList.children.length === 0) {
                            attachmentsList.innerHTML = `
                                <div class="col-12">
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-paperclip fs-1"></i>
                                        <p class="mt-2">No attachments yet. Upload files to share with your team!</p>
                                    </div>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete attachment. Please try again.');
                });
            }
        });
    </script>
@endsection
