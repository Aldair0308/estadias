<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'upload files']);
        Permission::create(['name' => 'view files']);
        Permission::create(['name' => 'edit files']);
        Permission::create(['name' => 'delete files']);

        // Create template permissions
        Permission::create(['name' => 'create templates']);
        Permission::create(['name' => 'view templates']);
        Permission::create(['name' => 'edit templates']);
        Permission::create(['name' => 'delete templates']);

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'upload files',
            'view files',
            'edit files',
            'delete files',
            'create templates',
            'view templates',
            'edit templates',
            'delete templates'
        ]);

        $tutorRole = Role::create(['name' => 'tutor']);
        $tutorRole->givePermissionTo([
            'upload files',
            'view files',
            'edit files',
            'delete files'
        ]);

        $studentRole = Role::create(['name' => 'student']);
        $studentRole->givePermissionTo([
            'upload files',
            'view files'
        ]);

        // Assign roles to existing users (optional)
        // You can modify this part based on your needs
        // For example, assign the first user as tutor and others as students
        $users = User::all();
        if ($users->count() > 0) {
            $users->first()->assignRole('tutor');
            $users->skip(1)->each(function ($user) {
                $user->assignRole('student');
            });
        }
    }
}