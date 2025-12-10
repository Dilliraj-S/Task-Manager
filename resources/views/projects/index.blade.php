@extends('layouts.app')
@section('title')
    Projects 
@endsection
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2>Projects</h2>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Add Project</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            @foreach($projects as $project)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $project->name }}</h5>
                                @if($project->user_id == Auth::id())
                                    <span class="badge bg-primary">Owner</span>
                                @else
                                    <span class="badge bg-info">Team Member</span>
                                @endif
                            </div>
                            <p class="card-text">{{ $project->description }}</p>
                            <p class="card-text">
                                <strong>Status:</strong> {{ $project->computed_status == 'pending' ? 'Pending' : ($project->computed_status == 'on_going' ? 'In Progress' : ($project->computed_status == 'unfinished' ? 'Unfinished' : 'Completed')) }}<br>
                                <strong>Deadline:</strong> 
                                @if($project->end_date && $project->end_date->isFuture())
                                    {{ $project->end_date->diffForHumans() }}
                                @else
                                    <span class="text-danger">Deadline Passed</span>
                                @endif
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('projects.tasks.index', $project->id) }}" class="btn btn-primary btn-sm"> <i class="bi bi-list"></i> Tasks</a>
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary btn-sm"> <i class="bi bi-eye"></i> View</a>
                                @if($project->user_id == Auth::id())
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm"> <i class="bi bi-pencil-square"></i> Edit</a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this project?')"> <i class="bi bi-trash"></i> Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
