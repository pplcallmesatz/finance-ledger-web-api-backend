<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
    public function index(Request $request): UserCollection
    {
        $this->authorize('view-any', User::class);
        $search = $request->get('search', '');

        $users = User::search($search)
            ->orderBy('name', 'asc')
            ->paginate();

        return new UserCollection($users);
    }

    public function store(UserStoreRequest $request): UserResource
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $user = User::create($validated);

        return new UserResource($user);
    }

    public function show(Request $request, User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        $user->update($validated);

        return new UserResource($user);
    }

    public function destroy(Request $request, User $user): Response
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }

    /**
     * Get detailed info for a user, including sales summary and entries.
     */
    public function userDetails($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $salesLedgers = $user->salesLedgers()->get();

        $pendingEntries = $salesLedgers->where('payment_status', 'pending')->values();
        $paidEntries = $salesLedgers->where('payment_status', 'paid')->values();

        $totalPending = $pendingEntries->sum('total_customer_price');
        $totalPaid = $paidEntries->sum('total_customer_price');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'remarks' => $user->remarks,
            ],
            'total_pending' => $totalPending,
            'total_paid' => $totalPaid,
            'pending_entries' => $pendingEntries,
            'paid_entries' => $paidEntries,
        ]);
    }
}
