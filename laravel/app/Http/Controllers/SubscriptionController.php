<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriptionResource;
use App\Http\Resources\SubscriptionCollection;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('subscriber')->paginate(10);
        return new SubscriptionCollection($subscriptions);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'subscriber_id' => 'required|exists:subscribers,id',
                'service' => 'required|string',
                'topic' => 'required|string',
                'expired_at' => 'date'
            ]);

            $subscription = Subscription::create($validated);
            return new SubscriptionResource($subscription);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Subscription $subscription)
    {
        return new SubscriptionResource($subscription);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'subscriber_id' => 'exists:subscribers,id',
            'service' => 'string',
            'topic' => 'string',
            'expired_at' => 'date'
        ]);

        $subscription->update($validated);
        return new SubscriptionResource($subscription);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->noContent();
    }
}