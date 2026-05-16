<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Version;

class VersionPreviewController extends Controller
{
    public function __invoke(?Version $version = null)
    {
        $sampleVersion = $version ?? Version::query()->latest()->first();

        if (! $sampleVersion) {
            $sampleVersion = new Version;
            $sampleVersion->version_number = '1.0.0';
            $sampleVersion->alert_title = 'Platform update available';
            $sampleVersion->description = 'Sample version description.';
            $sampleVersion->created_at = now();
        }

        return view('emails.version-notification', [
            'version' => $sampleVersion,
            'user' => auth()->user(),
            'appName' => config('app.name'),
            'appUrl' => config('app.url'),
        ]);
    }
}
