<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    // ─── Non-officer (read-only): View all announcements ──────────────────────

    public function index(Request $request): View
    {
        abort_if(Auth::user()->isAdmin(), 403);

        $search = trim($request->query('search', ''));

        $query = Announcement::with('user')
            ->orderByDesc('announcement_time');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(15)->appends($request->query());

        return view('announcements.index', compact('announcements', 'search'));
    }

    // ─── Rental Officer: Manage their announcements ────────────────────────────

    public function officerIndex(Request $request): View
    {
        $search = trim($request->query('search', ''));

        $query = Announcement::with('user')
            ->where('user_id', Auth::id())
            ->orderByDesc('announcement_time');

        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        $announcements = $query->paginate(20)->appends($request->query());

        return view('announcements.officer.index', compact('announcements', 'search'));
    }

    public function create(): View
    {
        return view('announcements.officer.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'message'           => ['required', 'string', 'max:5000'],
            'announcement_time' => ['required', 'date'],
        ]);

        Announcement::create([
            'user_id'           => Auth::id(),
            'title'             => $data['title'],
            'message'           => $data['message'],
            'announcement_time' => $data['announcement_time'],
        ]);

        return redirect()->route('officer.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    public function edit(Announcement $announcement): View
    {
        // Rental officer can only edit their own
        abort_if($announcement->user_id !== Auth::id(), 403);

        return view('announcements.officer.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_if($announcement->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'message'           => ['required', 'string', 'max:5000'],
            'announcement_time' => ['required', 'date'],
        ]);

        $announcement->update([
            'title'             => $data['title'],
            'message'           => $data['message'],
            'announcement_time' => $data['announcement_time'],
        ]);

        return redirect()->route('officer.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        abort_if($announcement->user_id !== Auth::id(), 403);

        $announcement->delete();

        return redirect()->route('officer.announcements.index')
            ->with('success', 'Announcement deleted.');
    }
}
