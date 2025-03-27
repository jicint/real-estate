<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Property::with('category');

            // Add filters
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

            // Search by title or description
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            }

            $properties = $query->latest()->get()->map(function ($property) {
                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'description' => $property->description,
                    'price' => $property->price,
                    'category_id' => $property->category_id,
                    'category_name' => $property->category->name,
                    'address' => $property->address,
                    'bedrooms' => $property->bedrooms,
                    'bathrooms' => $property->bathrooms,
                    'area' => $property->area,
                    'images' => $property->images,
                    'status' => $property->status,
                    'is_featured' => $property->is_featured,
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                    'created_at' => $property->created_at,
                    'updated_at' => $property->updated_at
                ];
            });

            // Debug log
            \Log::info('Properties count: ' . $properties->count());

            return response()->json([
                'status' => 'success',
                'data' => $properties,
                'count' => $properties->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'area' => 'nullable|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $property = Property::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('properties', 'public');
                $images[] = $path;
            }
            $property->images = $images;
            $property->save();
        }

        return response()->json($property, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::with(['category'])->findOrFail($id);
        return response()->json($property);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'category_id' => 'sometimes|exists:categories,id',
            'address' => 'sometimes|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'area' => 'nullable|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $property->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $images = $property->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('properties', 'public');
                $images[] = $path;
            }
            $property->images = $images;
            $property->save();
        }

        return response()->json($property);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::findOrFail($id);
        
        // Delete associated images
        if (!empty($property->images)) {
            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $property->delete();
        return response()->json(null, 204);
    }

    // Additional methods
    public function featured()
    {
        return Property::where('is_featured', true)
                      ->latest()
                      ->take(6)
                      ->get();
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }
}
