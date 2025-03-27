<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShareComparison;
use Illuminate\Support\Facades\Log;

class ComparisonShareController extends Controller
{
    public function shareViaEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'comparison' => 'required|array',
            'shareUrl' => 'required|url',
            'message' => 'nullable|string'
        ]);

        try {
            // Log the attempt
            Log::info('Attempting to send email', [
                'to' => $request->email,
                'comparison' => $request->comparison
            ]);

            Mail::to($request->email)->send(new ShareComparison(
                $request->comparison,
                $request->shareUrl,
                $request->message
            ));

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully'
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Email sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
} 