<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function getMultipleProperties(Request $request)
    {
        $propertyIds = $request->input('propertyIds', []);
        
        $properties = Property::whereIn('id', $propertyIds)
            ->with(['images', 'category'])
            ->get();

        return response()->json($properties);
    }

    public function getSharedProperties(Request $request)
    {
        $propertyIds = $request->query('ids', []);
        
        if (!is_array($propertyIds)) {
            $propertyIds = explode(',', $propertyIds);
        }

        $properties = Property::whereIn('id', $propertyIds)
            ->with(['images', 'category'])
            ->get();

        return response()->json($properties);
    }

    public function getPropertyDetails(Request $request)
    {
        $propertyIds = $request->input('propertyIds', []);
        
        $properties = Property::whereIn('id', $propertyIds)
            ->with(['images', 'category'])
            ->get();

        return response()->json($properties);
    }

    public function index()
    {
        $properties = Property::all();
        return response()->json($properties);
    }
} 