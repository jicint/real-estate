<?php

namespace App\Http\Controllers;

use App\Models\SavedComparison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComparisonController extends Controller
{
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Add debug logging
        Log::info('Comparison save attempt started', [
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        try {
            $validated = $request->validate([
                'property_ids' => 'required|array',
                'name' => 'required|string|max:255'
            ]);

            Log::info('Validation passed', ['validated_data' => $validated]);

            $comparison = SavedComparison::create([
                'user_id' => Auth::id(),
                'property_ids' => $validated['property_ids'],
                'name' => $validated['name']
            ]);

            Log::info('Comparison saved successfully', ['comparison' => $comparison]);

            return response()->json([
                'success' => true,
                'message' => 'Comparison saved successfully',
                'comparison' => $comparison
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving comparison', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save comparison: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $comparisons = SavedComparison::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comparisons);
    }
} 