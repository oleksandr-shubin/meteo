<?php

namespace App\Domain\Subscription\Controllers;

use App\Domain\Subscription\Actions\StoreSubscriptionAction;
use App\Domain\Subscription\Actions\UpdateSubscriptionAction;
use App\Domain\Subscription\Models\Subscription;
use App\Domain\Subscription\Requests\StoreSubscriptionRequest;
use App\Domain\Subscription\Requests\UpdateSubscriptionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SubscriptionController extends Controller
{
    private const PER_PAGE = 10;

    public function __construct(
        private readonly StoreSubscriptionAction $storeSubscriptionAction,
        private readonly UpdateSubscriptionAction $updateSubscriptionAction,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $paginator = $request->user()->subscriptions()
            ->with('city')
            ->latest()
            ->paginate(self::PER_PAGE);

        return view('subscription.index', compact('paginator'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subscription.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $this->storeSubscriptionAction->execute($request->toArray());

        $request->session()->flash('success', 'Subscription was successfully created!');
        return Redirect::route('subscription.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        $this->authorize('update', $subscription);
        return view('subscription.edit', compact('subscription'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);
        $this->updateSubscriptionAction->execute($subscription, $request->toArray());

        $request->session()->flash('success', 'Subscription was successfully updated!');
        return Redirect::route('subscription.edit', ['subscription' => $subscription->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);
        $subscription->delete();

        request()->session()->flash('success', 'Subscription was successfully deleted!');
        return Redirect::route('subscription.index');
    }
}
