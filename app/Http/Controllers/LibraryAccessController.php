<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\VisitorStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LibraryAccessController extends Controller
{
    /**
     * Show the access form.
     */
    public function showAccessForm()
    {
        return view('library.access');
    }

    /**
     * Process the access request.
     */
    public function processAccess(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'consent' => 'required|accepted',
            'nda_consent' => 'required|accepted',
        ]);

        // Generate a new access token
        $token = AccessToken::generate(
            $validated['name'],
            $validated['email']
        );

        // Record visitor statistics
        VisitorStat::recordVisit([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_visited' => 'library.index',
            'access_method' => 'token',
            'has_consent' => true,
        ]);

        // Store the token in session
        Session::put('library_access_token', $token->token);

        return redirect()->route('library.index');
    }

    /**
     * Access the library with a token.
     */
    public function accessWithToken(Request $request, $token)
    {
        $accessToken = AccessToken::findValidToken($token);

        if (!$accessToken || !$accessToken->isValid()) {
            return redirect()->route('library.access')
                ->with('error', 'Invalid or expired access token.');
        }

        // Mark the token as used
        $accessToken->markAsUsed();

        // Record visitor statistics
        VisitorStat::recordVisit([
            'name' => $accessToken->name,
            'email' => $accessToken->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_visited' => 'library.index',
            'access_method' => 'token_link',
            'has_consent' => true,  // Consent is implied when using a token link
        ]);

        // Store the token in session
        Session::put('library_access_token', $token);

        return redirect()->route('library.index');
    }

    /**
     * Check if the user has access to the library.
     */
    public static function hasAccess()
    {
        $token = Session::get('library_access_token');

        if (!$token) {
            return false;
        }

        $accessToken = AccessToken::findValidToken($token);

        return $accessToken && $accessToken->isValid();
    }

    /**
     * Revoke library access.
     */
    public function revokeAccess()
    {
        Session::forget('library_access_token');

        return redirect()->route('library.access')
            ->with('success', 'You have been logged out successfully.');
    }
}
