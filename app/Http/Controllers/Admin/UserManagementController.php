<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display all users except admin accounts.
     * Admin can see: staff, student, guest, rental_officer.
     */
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $search = trim($request->query('search', ''));

        $query = User::whereIn('role', ['staff', 'student', 'guest', 'rental_officer'])
            ->orderBy('role')
            ->orderBy('fullname');

        if ($filter === 'staff') {
            $query->where('role', 'staff');
        } elseif ($filter === 'student') {
            $query->where('role', 'student');
        } elseif ($filter === 'guest') {
            $query->where('role', 'guest');
        } elseif ($filter === 'rental_officer') {
            $query->where('role', 'rental_officer');
        }
        // 'all' applies no additional filter

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('matric_number', 'like', "%{$search}%")
                  ->orWhere('staff_id', 'like', "%{$search}%")
                  ->orWhere('ic_number', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20)->appends($request->query());

        return view('admin.users.index', compact('users', 'filter', 'search'));
    }

    /**
     * Promote a Staff, Student, or Guest to Rental Officer.
     */
    public function promote(User $user): RedirectResponse
    {
        if (!in_array($user->role, ['staff', 'student', 'guest'])) {
            return back()->with('error', 'Only Staff, Student, or Guest users can be promoted to Rental Officer.');
        }

        $user->update(['role' => 'rental_officer']);

        return back()->with('success', "{$user->fullname} has been promoted to Rental Officer.");
    }

    /**
     * Demote a Rental Officer back to their original role derived from email.
     */
    public function demote(User $user): RedirectResponse
    {
        if ($user->role !== 'rental_officer') {
            return back()->with('error', 'This user is not a Rental Officer.');
        }

        $originalRole = $user->deriveRoleFromEmail();
        $user->update(['role' => $originalRole]);

        return back()->with('success', "{$user->fullname} has been demoted back to " . ucfirst($originalRole) . ".");
    }
}