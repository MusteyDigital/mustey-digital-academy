<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:student,instructor,admin'],
        ]);

        // prevent changing your own role
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $old = $user->role;

        // ✅ bulletproof save (works even if fillable is not configured)
        $user->role = $data['role'];
        $user->save();

        return back()->with('success', "Role updated: {$user->name} ({$old} → {$user->role}).");
    }

    // ✅ Export CSV (this fixes Route [admin.users.export] not defined)
    public function export(Request $request): StreamedResponse
    {
        $q = (string) $request->query('q', '');

        $query = User::query()
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id');

        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // header row
            fputcsv($out, ['id', 'name', 'email', 'role', 'created_at']);

            $query->chunk(500, function ($users) use ($out) {
                foreach ($users as $u) {
                    fputcsv($out, [
                        $u->id,
                        $u->name,
                        $u->email,
                        $u->role,
                        optional($u->created_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
