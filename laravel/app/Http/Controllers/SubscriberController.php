<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriberResource;
use App\Http\Resources\SubscriberCollection;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::paginate(10);
        return new SubscriberCollection($subscribers);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:subscribers',
                'name' => 'required|string|max:255'
            ]);
    
            $subscriber = Subscriber::create($validated);
            return new SubscriberResource($subscriber);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating subscriber',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Subscriber $subscriber)
    {
        return new SubscriberResource($subscriber);
    }

    public function update(Request $request, Subscriber $subscriber)
    {
        $validated = $request->validate([
            'email' => 'email|unique:subscribers,email,' . $subscriber->id,
            'name' => 'string|max:255'
        ]);

        $subscriber->update($validated);
        return new SubscriberResource($subscriber);
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();
        return response()->noContent();
    }
}
