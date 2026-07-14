<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->load('role.permissions');
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->load('role.permissions');
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->load('role.permissions');
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): JsonResponse
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        
        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $user = $this->findOrCreateUser($googleUser);
            
            $otpCode = (string) rand(100000, 999999);
            
            $user->update([
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10),
            ]);
            
            Mail::to($user->email)->send(new SendOtpMail($otpCode));
            
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            
            return redirect($frontendUrl . '/guest/verify-otp?email=' . urlencode($user->email));
        } catch (Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect($frontendUrl . '/guest/login?error=' . urlencode('Google authentication failed: ' . $e->getMessage()));
        }
    }

    /**
     * Exchange a Google access token directly for a Sanctum token.
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        try {
            $googleUser = Socialite::driver('google')->userFromToken($validated['token']);

            
            $user = $this->findOrCreateUser($googleUser);
            $user->load('role.permissions');
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Google token validation failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Find or create a user from a Google user object.
     */
    protected function findOrCreateUser($googleUser): User
    {
        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }
            return $user;
        }

        $staffRole = \App\Models\Role::query()->where('slug', 'staff')->first();

        return User::query()->create([
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'password' => null,
            'phone' => '0' . substr($googleUser->getId(), -10),
            'date_of_birth' => '2000-01-01',
            'store_no' => 'N/A',
            'position' => \App\Enums\Positions::CASHIER,
            'role_id' => $staffRole?->id,
            'department' => 'Sales',
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || $user->otp_code !== $validated['otp_code'] || ! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return response()->json([
                'message' => 'Invalid or expired verification code.',
                'errors' => [
                    'otp_code' => ['The verification code is invalid or has expired.'],
                ],
            ], 422);
        }

        // Clear OTP and mark verified
        $user->forceFill([
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $user->load('role.permissions');
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $otpCode = (string) rand(100000, 999999);

        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otpCode));

        return response()->json([
            'message' => 'Verification code resent successfully.',
        ]);
    }
}
