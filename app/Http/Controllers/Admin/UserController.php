<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->latest()
            ->paginate(20);

        return Inertia::render('admin/users/index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('admin/users/create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data             = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('toast', ['type' => 'success', 'message' => 'Usuário criado com sucesso.']);
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('admin/users/edit', [
            'user' => $user->only('id', 'name', 'email', 'role'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        return redirect()->route('admin.users.index')
            ->with('toast', ['type' => 'success', 'message' => 'Usuário atualizado com sucesso.']);
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('toast', ['type' => 'success', 'message' => 'Usuário removido com sucesso.']);
    }
}
