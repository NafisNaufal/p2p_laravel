<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPin;
use Illuminate\Support\Str;

class P2PController extends Controller
{
    public function index()
    {
        // Check if user has PIN set in session
        if (!session()->has('user_session_id')) {
            return redirect()->route('pin.setup');
        }

        $userPin = UserPin::where('session_id', session('user_session_id'))->first();
        if (!$userPin || !$userPin->is_active) {
            return redirect()->route('pin.setup');
        }

        return view('p2p.dashboard', compact('userPin'));
    }

    public function showPinSetup()
    {
        return view('p2p.pin-setup');
    }

    public function setupPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
            'username' => 'nullable|string|max:50'
        ]);

        $sessionId = Str::uuid();
        $userPin = new UserPin();
        $userPin->session_id = $sessionId;
        $userPin->setPin($request->pin);
        $userPin->username = $request->username ?? 'Anonymous';
        $userPin->ip_address = $request->ip();
        $userPin->save();

        session(['user_session_id' => $sessionId]);

        return redirect()->route('p2p.dashboard')->with('success', 'PIN berhasil diatur!');
    }

    public function showPinLogin()
    {
        return view('p2p.pin-login');
    }

    public function loginPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4'
        ]);

        $userPin = UserPin::where('ip_address', $request->ip())
            ->where('is_active', true)
            ->first();

        if (!$userPin || !$userPin->verifyPin($request->pin)) {
            return back()->withErrors(['pin' => 'PIN salah atau belum diatur']);
        }

        session(['user_session_id' => $userPin->session_id]);

        return redirect()->route('p2p.dashboard')->with('success', 'Login berhasil!');
    }

    public function logout()
    {
        session()->forget('user_session_id');
        return redirect()->route('pin.setup');
    }
}
