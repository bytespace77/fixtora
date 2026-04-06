<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'company_name' => ['required', 'string', 'max:255'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        // Check if company already exists by name
        $company = Company::where('name', $data['company_name'])->first();

        if (!$company) {
            $company = Company::create([
                'name' => $data['company_name'],
                'slug' => Str::slug($data['company_name']) . '-' . Str::random(4),
            ]);
        }

        // Find or create the default "user" role
        $userRole = Role::whereRaw('LOWER(TRIM(name)) = ?', ['user'])->first();
        if (!$userRole) {
            $userRole = Role::create([
                'name'        => 'user',
                'description' => 'Default role for self-registered accounts.',
                'permissions' => [],
            ]);
        }

        // Task 36: Create the user linked to company, role = admin (first user = company admin)
        return User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'company_id' => $company->id,
            'role'       => 'user',    // ← all self-registered users are 'user', only superadmin is seeded manually
            'role_id'    => $userRole->id,
        ]);
    }
}