<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GoogleController extends Controller
{
    private GoogleDriveService $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function redirect()
    {
        $authUrl = $this->googleDriveService->getAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        try {
            $code = $request->get('code');
            
            if (!$code) {
                return redirect()->route('login')->with('error', 'Authorization failed');
            }

            $token = $this->googleDriveService->getAccessToken($code);
            
            // Get user info from Google
            $this->googleDriveService->setAccessToken($token['access_token']);
            
            // For now, we'll create a simple user. In production, you'd want to get user info from Google
            $user = User::updateOrCreate(
                ['email' => 'user@example.com'], // You'd get this from Google API
                [
                    'name' => 'Google User',
                    'google_id' => 'google_' . time(),
                    'google_drive_token' => $token['access_token'],
                    'google_drive_refresh_token' => $token['refresh_token'] ?? null,
                ]
            );

            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login failed: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
