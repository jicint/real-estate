@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Shared Property Comparison</h1>
        
        <div id="shared-comparison">
            <shared-comparison 
                :initial-properties="{{ json_encode($comparisonData['properties']) }}"
                :comparison-name="{{ json_encode($comparisonData['name'] ?? 'Shared Comparison') }}"
            ></shared-comparison>
        </div>
    </div>
</div>
@endsection 