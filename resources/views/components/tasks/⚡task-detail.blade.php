<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Task;

class TaskDetail extends Component
{
    public Task $task;
    public $newComment = '';

    protected $rules = [
        'newComment' => 'required|string|max:1000',
    ];

    public function mount(Task $task)
    {
        $this->task = $task;
    }

    public function updateStatus($newStatus)
    {
        $this->task->status = $newStatus;
        $this->task->save();
    }

    public function addComment()
    {
        $this->validate();
        $this->task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->newComment,
        ]);
        $this->newComment = '';
        $this->dispatch('comment-added');
    }

    public function render()
    {
        return view('livewire.tasks.task-detail', [
            'comments' => $this->task->comments()->with('user')->latest()->get(),
            'statusHistory' => $this->task->statusChanges()->with('changedBy')->latest()->get(),
        ]);
    }
}
?>

<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
        <h1 class="text-2xl font-bold break-words">{{ $task->title }}</h1>
        <select wire:change="updateStatus($event.target.value)" class="mt-2 sm:mt-0 border rounded dark:bg-gray-800 px-3 py-1">
            @foreach(['todo','in_progress','in_review','blocked','done'] as $status)
                <option value="{{ $status }}" @if($task->status === $status) selected @endif>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <div><strong>Priority:</strong> {{ ucfirst($task->priority) }}</div>
        <div><strong>Due:</strong> {{ $task->due_date ? $task->due_date->format('Y-m-d') : 'No due date' }}</div>
        <div><strong>Project:</strong> {{ $task->project->name }}</div>
    </div>
    <div class="mt-4">
        <h3 class="font-semibold">Description</h3>
        <p class="whitespace-pre-wrap">{{ $task->description ?? 'No description' }}</p>
    </div>
    <div class="mt-6">
        <h3 class="text-lg font-semibold">Comments</h3>
        <div class="space-y-2 mt-2 max-h-96 overflow-y-auto">
            @foreach($comments as $comment)
                <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded">
                    <strong>{{ $comment->user->name }}</strong> <span class="text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                    <p class="mt-1">{{ $comment->body }}</p>
                </div>
            @endforeach
        </div>
        <textarea wire:model="newComment" class="w-full border rounded dark:bg-gray-800 mt-3 p-2" rows="3" placeholder="Write a comment..."></textarea>
        <button wire:click="addComment" class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Post Comment</button>
    </div>
    <div class="mt-6">
        <h3 class="text-lg font-semibold">Status History</h3>
        <ul class="list-disc ml-5">
            @foreach($statusHistory as $change)
                <li>{{ $change->from_status ?? 'Start' }} → {{ $change->to_status }} by {{ $change->changedBy->name ?? 'System' }} at {{ $change->created_at->format('Y-m-d H:i') }}</li>
            @endforeach
        </ul>
    </div>
</div>
