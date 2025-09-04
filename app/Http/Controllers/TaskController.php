<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $assignedTo = $request->assigned_to;
        $teamId = $request->team_id;

        $tasks = Task::with(['owner', 'assigned', 'comments', 'team'])
            ->when($search, function ($q) use ($search) {
                return $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            })
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($assignedTo, function ($q) use ($assignedTo) {
                return $q->where('assigned_to', $assignedTo);
            })
            ->when($teamId, function ($q) use ($teamId) {
                return $q->where('team_id', $teamId);
            })
            ->latest()
            ->paginate(10);

        $users = User::all();
        $teams = Team::with('users')->get();

        return view('tasks.index', compact('tasks', 'users', 'teams', 'search', 'status', 'assignedTo', 'teamId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
            'deadline' => 'nullable|date',
            'task_docs.*' => 'nullable|file|mimes:pdf,doc,docx,xlsx,jpg,png',
        ]);

        $data['owner_id'] = auth()->id();

        if ($request->hasFile('task_docs')) {
            $docs = [];
            foreach ($request->file('task_docs') as $file) {
                $docs[] = $file->store('task_docs', 'public');
            }
            $data['task_docs'] = json_encode($docs);
        }

        Task::create($data);

        return redirect()->back()->with('success', 'Task created successfully');
    }

    public function show(Task $task)
    {

        // Load all necessary relationships
        $task->load([
            'owner',
            'assigned',
            'comments.user',
            'team',
            'team.radio'
        ]);

        // Add users and teams variables
        $users = User::all();
        $teams = Team::with('users')->get();

        return view('tasks.show', compact('task', 'users', 'teams'));
    }

    public function edit(Task $task)
    {
        $users = User::all();
        $teams = Team::with('users')->get();
        return view('tasks.edit', compact('task', 'users', 'teams'));
    }

    public function update(Request $request, Task $task)
{
    $data = $request->validate([
        'title' => 'required|string',
        'description' => 'required|string',
        'assigned_to' => 'required|exists:users,id',
        'team_id' => 'nullable|exists:teams,id',
        'deadline' => 'nullable|date',
        'status' => 'required|in:todo,pending,done,late,expired',
        'task_docs.*' => 'nullable|file|mimes:pdf,doc,docx,xlsx,jpg,png',
    ]);

    // Get existing files
    $existingFiles = [];
    if ($task->task_docs) {
        $existingFiles = json_decode($task->task_docs, true);
    }

    if ($request->hasFile('task_docs')) {
        $newDocs = [];
        foreach ($request->file('task_docs') as $file) {
            // Get original filename with extension
            $originalName = $file->getClientOriginalName();
            
            // Generate a unique filename to avoid conflicts
            $filename = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueName = $filename . '_' . time() . '.' . $extension;
            
            // Store file with unique name
            $newDocs[] = $file->storeAs('task_docs', $uniqueName, 'public');
        }
        
        // Merge existing files with new files
        $allFiles = array_merge($existingFiles, $newDocs);
        $data['task_docs'] = json_encode($allFiles);
    } else {
        // Keep existing files if no new files uploaded
        $data['task_docs'] = json_encode($existingFiles);
    }

    $task->update($data);
    return redirect()->back()->with('success', 'Task updated successfully');
}

    public function updateStatus(Request $request, Task $task)
    {

        $request->validate([
            'status' => 'required|in:todo,pending,done,late,expired'
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated successfully');
    }
    public function destroy(Task $task)
    {
        // Delete associated files
        if ($task->task_docs) {
            $docs = json_decode($task->task_docs, true);
            foreach ($docs as $doc) {
                Storage::disk('public')->delete($doc);
            }
        }

        $task->delete();
        return redirect()->back()->with('success', 'Task deleted successfully');
    }
    public function removeFile(Request $request, Task $task)
    {
        $filePath = $request->file_path;

        // Remove file from storage
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        // Remove file from task_docs array
        $currentDocs = json_decode($task->task_docs, true) ?? [];
        $updatedDocs = array_filter($currentDocs, function ($doc) use ($filePath) {
            return $doc !== $filePath;
        });

        $task->update(['task_docs' => json_encode(array_values($updatedDocs))]);

        return redirect()->back()->with('success', 'File removed successfully');
    }

    public function addComment(Request $request, Task $task)
    {
        $request->validate(['comment' => 'required|string']);
        $task->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);
        return redirect()->back()->with('success', 'Comment added successfully');
    }
}
