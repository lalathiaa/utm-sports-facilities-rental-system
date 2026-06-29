<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Determine role from email domain
        $role = 'guest';
        if (str_ends_with($request->email, '@graduate.utm.my')) {
            $role = 'student';
        } elseif (str_ends_with($request->email, '@utm.my')) {
            $role = 'staff';
        }

        // Base validation rules
        $rules = [
            'fullname'  => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'alpha_dash', 'max:50', 'unique:' . User::class],
            'ic_number' => ['required', 'string', 'max:20'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Conditional fields based on role
        if ($role === 'student') {
            $rules['matric_number'] = ['required', 'string', 'max:20'];
        }
        if ($role === 'staff') {
            $rules['staff_id'] = ['required', 'string', 'max:20'];
        }

        $request->validate($rules);

        $user = User::create([
            'fullname'      => $request->fullname,
            'username'      => $request->username,
            'ic_number'     => $request->ic_number,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $role,
            'status'        => 'active',
            'matric_number' => $role === 'student' ? $request->matric_number : null,
            'staff_id'      => $role === 'staff' ? $request->staff_id : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
