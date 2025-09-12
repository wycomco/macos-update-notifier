<?php

namespace App\Http\Controllers;

use App\Models\Release;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubscriberImportController extends Controller
{
    /**
     * Show import form
     */
    public function create(Request $request)
    {
        $availableVersions = Release::distinct('major_version')
            ->orderBy('major_version')
            ->pluck('major_version')
            ->toArray();
            
        if (empty($availableVersions)) {
            $availableVersions = ['macOS 14', 'macOS 15'];
        }
        
        // Get import method from request (default to 'textarea')
        $method = $request->get('method', 'textarea');
        
        return view('subscribers.import', compact('availableVersions', 'method'));
    }

    /**
     * Process bulk import
     */
    public function store(Request $request)
    {
        // Handle CSV file upload
        if ($request->hasFile('csv_file')) {
            return $this->processCsvImport($request);
        }
        
        // Handle textarea input only if no CSV file is present
        if (!$request->hasFile('csv_file') && $request->has('emails')) {
            return $this->processTextareaImport($request);
        }
        
        // If neither CSV file nor emails are provided, validate appropriately
        if (!$request->hasFile('csv_file') && !$request->has('emails')) {
            return back()->withErrors(['csv_file' => 'Please upload a CSV file or provide email addresses.']);
        }
        
        return back()->withErrors(['general' => 'Invalid import method.']);
    }
    
    /**
     * Process textarea import
     */
    private function processTextareaImport(Request $request)
    {
        $request->validate([
            'emails' => ['required', 'string'],
            'subscribed_versions' => ['required', 'array', 'min:1'],
            'subscribed_versions.*' => ['string'],
            'days_to_install' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $availableVersions = Release::distinct('major_version')
            ->orderBy('major_version')
            ->pluck('major_version')
            ->toArray();
            
        if (empty($availableVersions)) {
            $availableVersions = ['macOS 14', 'macOS 15'];
        }

        // Validate versions
        foreach ($request->subscribed_versions as $version) {
            if (!in_array($version, $availableVersions)) {
                return back()->withErrors(['subscribed_versions' => "Invalid version: {$version}"]);
            }
        }

        // Parse emails with improved delimiter handling
        $emailText = $request->emails;
        
        // Replace common delimiters with newlines
        $emailText = str_replace([',', ';', '\t'], "\n", $emailText);
        
        // Split by newlines and clean up
        $emails = collect(explode("\n", $emailText))
            ->map(fn($email) => trim($email))
            ->filter(fn($email) => !empty($email))
            ->unique();

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($emails as $email) {
            // Validate email format
            $validator = Validator::make(['email' => $email], [
                'email' => ['required', 'email'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Invalid email format: {$email}";
                continue;
            }

            // Check if subscriber already exists
            $existingSubscriber = Subscriber::where('email', $email)->first();
            
            if ($existingSubscriber) {
                $skipped++;
                continue;
            }

            // Create new subscriber
            $subscriber = Subscriber::create([
                'email' => $email,
                'subscribed_versions' => $request->subscribed_versions,
                'days_to_install' => $request->days_to_install,
                'admin_id' => Auth::id(),
            ]);

            // Log import action
            $subscriber->actions()->create([
                'action' => 'imported',
                'data' => [
                    'imported_by' => Auth::user()->email,
                    'method' => 'textarea',
                    'versions' => $request->subscribed_versions,
                    'days_to_install' => $request->days_to_install,
                ],
            ]);

            $imported++;
        }

        $message = "Import completed: {$imported} imported, {$skipped} skipped";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " and " . (count($errors) - 3) . " more";
            }
        }

        return redirect()->route('subscribers.index')->with('success', $message);
    }
    
    /**
     * Process CSV file import
     */
    private function processCsvImport(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
            'subscribed_versions' => ['required', 'array', 'min:1'],
            'subscribed_versions.*' => ['string'],
            'days_to_install' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $availableVersions = Release::distinct('major_version')
            ->orderBy('major_version')
            ->pluck('major_version')
            ->toArray();
            
        if (empty($availableVersions)) {
            $availableVersions = ['macOS 14', 'macOS 15'];
        }

        // Validate versions
        foreach ($request->subscribed_versions as $version) {
            if (!in_array($version, $availableVersions)) {
                return back()->withErrors(['subscribed_versions' => "Invalid version: {$version}"]);
            }
        }

        $file = $request->file('csv_file');
        $content = file_get_contents($file->getRealPath());
        $lines = explode("\n", $content);
        
        // Remove header if present (detect common header patterns)
        if (count($lines) > 0) {
            $firstLine = strtolower(trim($lines[0]));
            if (strpos($firstLine, 'email') !== false || strpos($firstLine, 'address') !== false) {
                array_shift($lines);
            }
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Handle both CSV and simple email lists
            $parts = str_getcsv($line);
            $email = trim($parts[0]);
            
            // Validate email format
            $validator = Validator::make(['email' => $email], [
                'email' => ['required', 'email'],
            ]);

            if ($validator->fails()) {
                $errors[] = "Line " . ($lineNumber + 1) . ": Invalid email format '{$email}'";
                continue;
            }

            // Check if subscriber already exists
            $existingSubscriber = Subscriber::where('email', $email)->first();
            
            if ($existingSubscriber) {
                $skipped++;
                continue;
            }

            // Create new subscriber
            $subscriber = Subscriber::create([
                'email' => $email,
                'subscribed_versions' => $request->subscribed_versions,
                'days_to_install' => $request->days_to_install,
                'admin_id' => Auth::id(),
            ]);

            // Log import action
            $subscriber->actions()->create([
                'action' => 'imported',
                'data' => [
                    'imported_by' => Auth::user()->email,
                    'method' => 'csv',
                    'versions' => $request->subscribed_versions,
                    'days_to_install' => $request->days_to_install,
                ],
            ]);

            $imported++;
        }

        $message = "CSV import completed: {$imported} imported, {$skipped} skipped";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " and " . (count($errors) - 3) . " more";
            }
        }

        return redirect()->route('subscribers.index')->with('success', $message);
    }
}
