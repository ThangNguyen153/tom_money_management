<?php

namespace Database\Seeders;

use App\Models\TMM_User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleSuperAdmin = Role::findOrCreate('super-administrator','api');
        $roleAdmin = Role::findOrCreate('administrator','api');
        $roleUser = Role::findOrCreate('user','api');
        $user = TMM_User::find(1);
        if($user)
            $user->assignRole('super-administrator');
        //$permission = Permission::create(['name' => 'edit articles']);
    }
}
