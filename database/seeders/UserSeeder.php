<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'company show',
            'company create',
            'company edit',
            'company delete',
            'employee show',
            'employee create',
            'employee edit',
            'employee delete',
        ];

        foreach($permissions as $permission){
            Permission::create(['name' => $permission]);
        }

        //Create admin role
        $admin_role = Role::create(['name' => 'admin']);
        //Admin permission
        $admin_role->givePermissionTo(Permission::all());

        //Create company role and permission
        $company_role = Role::create(['name' => 'company']);
        $company_role->givePermissionTo(['company show','company edit','employee show','employee create','employee edit','employee delete']);

        ////Create user for admin
        $admin = User::create([
            'name'      => 'Admin',
            'email'     => 'admin@test.com',
            'password'  => 'password'
        ]);
        
        $admin->assignRole($admin_role);
        //Admin permission
        $admin->givePermissionTo(Permission::all());

        //Create user for company role
        $company = User::create([
            'name'      => 'Company',
            'email'     => 'company@test.com',
            'password'  => 'password'
        ]);
        
        $company->assignRole($company_role);
        //Company permission
        $company->givePermissionTo(['company show','company edit','employee show','employee create','employee edit','employee delete']);
    }
}
