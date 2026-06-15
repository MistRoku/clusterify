<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;

class CompanyList extends Component
{
    use WithPagination;

    public function suspend($id)
    {
        $company = Company::findOrFail($id);
        $company->is_active = !$company->is_active;
        $company->save();
    }

    public function delete($id)
    {
        $company = Company::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.company-list', [
            'companies' => Company::with('owner')->paginate(10),
        ]);
    }
}
?>

<div>
    <h1 class="text-2xl font-bold mb-4">Companies (Super Admin)</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr><th class="px-4 py-2">ID</th><th>Name</th><th>Subdomain</th><th>Status</th><th>Created By</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr class="border-b dark:border-gray-700">
                    <td class="px-4 py-2">{{ $company->id }}</td>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->subdomain }}</td>
                    <td>{{ $company->is_active ? 'Active' : 'Suspended' }}</td>
                    <td>{{ $company->owner->name ?? 'N/A' }}</td>
                    <td class="space-x-2">
                        <button wire:click="suspend({{ $company->id }})" class="text-yellow-600 hover:underline">Toggle Suspend</button>
                        <button wire:click="delete({{ $company->id }})" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $companies->links() }}
</div>
