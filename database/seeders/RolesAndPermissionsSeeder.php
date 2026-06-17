<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'manage company users']);
        Permission::create(['name' => 'create project']);
        Permission::create(['name' => 'edit project']);
        Permission::create(['name' => 'delete project']);
        Permission::create(['name' => 'manage tasks']);
        Permission::create(['name' => 'assign tasks']);
        Permission::create(['name' => 'view company logs']);

        $companyAdmin = Role::create(['name' => 'company_admin']);
        $companyAdmin->givePermissionTo(['manage company users', 'create project', 'edit project', 'delete project', 'manage tasks', 'assign tasks', 'view company logs']);

        $projectLead = Role::create(['name' => 'project_lead']);
        $projectLead->givePermissionTo(['create project', 'edit project', 'manage tasks', 'assign tasks']);

        $member = Role::create(['name' => 'member']);
        $member->givePermissionTo(['manage tasks']);

        $viewer = Role::create(['name' => 'viewer']);
        // no explicit permissions – read only via policies
    }
}
