<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\SalesLedger;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // $this->authorize('view-any', User::class);

        $search = $request->get('search', '');

        $users = User::search($search)
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('app.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        // $this->authorize('create', User::class);

        return view('app.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        // $this->authorize('create', User::class);

        $validated = $request->validated();

        $user = User::create($validated);

        return redirect()
            ->route('users.edit', $user)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user): View
    {
        // $this->authorize('view', $user);
        $getPurchaseDataCollection = SalesLedger::where('user_id', $user ->id)
                                        ->orderBy('sales_date', 'desc');                    
                                        
        $getPurchaseData = $getPurchaseDataCollection
                            ->paginate(25);
                            // dd($getPurchaseData);
        
        $totalPurchased = $getPurchaseDataCollection -> sum('total_customer_price');
        
        $getPurchasePendingData = $getPurchaseDataCollection
                                    -> WHERE('payment_status', 'pending')                           
                                    ->paginate(25);
        $totalPendingPayment = $getPurchaseDataCollection 
                                -> WHERE('payment_status', 'pending')                           
                                -> sum('total_customer_price');

        return view('app.users.show', compact('user', 'getPurchaseData', 'getPurchasePendingData','totalPurchased', 'totalPendingPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user): View
    {
        // $this->authorize('update', $user);

        return view('app.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UserUpdateRequest $request,
        User $user
    ): RedirectResponse {
        // $this->authorize('update', $user);

        $validated = $request->validated();

        $user->update($validated);

        return redirect()
            ->route('users.edit', $user)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('users.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
