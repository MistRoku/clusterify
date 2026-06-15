<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $company = $user->currentCompany();
        $tasks = $user->tasksAssigned()->whereNotIn('status', ['done'])->limit(10)->get();
        $projects = $company ? $company->projects()->limit(5)->get() : collect();
        return view('livewire.dashboard', compact('tasks', 'projects', 'company'));
    }
}
?>

<div>
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    @if($company)
        <p class="mb-4 text-gray-600 dark:text-gray-400">Welcome to <strong class="text-indigo-600">{{ $company->name }}</strong></p>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold mb-3">My Active Tasks</h2>
            @forelse($tasks as $task)
                <div class="border-b dark:border-gray-700 py-2 flex justify-between items-center">
                    <a href="{{ route('tasks.show', $task) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $task->title }}</a>
                    <span class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-700">{{ ucfirst($task->status) }}</span>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">No active tasks.</p>
            @endforelse
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold mb-3">Recent Projects</h2>
            @forelse($projects as $project)
                <div class="border-b dark:border-gray-700 py-2">{{ $project->name }}</div>
            @empty
                <p class="text-gray-500 dark:text-gray-400">No projects yet.</p>
            @endforelse
        </div>
    </div>
</div>
