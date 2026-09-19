<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Modules\Attendance\Services\AttendanceService;
use App\Modules\Settings\Models\UserFeatureSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * AuthController
 *
 * Menangani autentikasi API berbasis Sanctum personal access token:
 * register, register-with-features, login, logout, update-profile, dan me.
 */
class AuthController extends ApiController
{
    /**
     * Daftarkan user baru (self-register) — role default 'admin'.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        $token = $user->createToken('api-access')->plainTextToken;

        return $this->ok([
            'user' => $user,
            'token' => $token,
        ], 'Registrasi berhasil', 201);
    }

    /**
     * Admin mendaftarkan user baru dengan customisasi fitur (wizard).
     */
    public function registerWithFeatures(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['sometimes', 'string', 'in:admin,member,finance,supervisor,pic,employee,internship'],
            'features' => ['sometimes', 'array'],
            'features.*.key' => ['required', 'string', 'exists:feature_settings,key'],
            'features.*.is_enabled' => ['required', 'integer', 'in:0,1'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'member',
        ]);

        // Simpan customisasi fitur per-user
        if (! empty($data['features'])) {
            foreach ($data['features'] as $f) {
                UserFeatureSetting::create([
                    'user_id' => $user->id,
                    'feature_key' => $f['key'],
                    'is_enabled' => $f['is_enabled'],
                ]);
            }
        }

        return $this->ok([
            'user' => $user,
        ], 'User berhasil didaftarkan', 201);
    }

    /**
     * Login dengan bukti kredensial, kembalikan token baru bila valid.
     */
    public function login(Request $request, AttendanceService $attendance): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return $this->fail('Email atau password salah', 401);
        }

        /** @var User $user */
        $user = Auth::user();

        $attendance->logLogin($user->id, $request->ip(), $request->userAgent());

        return $this->ok([
            'user' => $user,
            'token' => $user->createToken('api-access')->plainTextToken,
        ], 'Login berhasil');
    }

    /**
     * Tampilkan profil user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->ok($request->user(), 'Profil user');
    }

    /**
     * Update profil user (name, email).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($data);

        return $this->ok($user->fresh(), 'Profil berhasil diperbarui');
    }

    /**
     * Cabut token aktif sehingga sesi API berakhir.
     */
    public function logout(Request $request, AttendanceService $attendance): JsonResponse
    {
        $user = $request->user();
        $attendance->logLogout($user->id, $request->ip(), $request->userAgent());
        $user->currentAccessToken()?->delete();

        return $this->ok([], 'Logout berhasil');
    }

    /**
     * Daftar seluruh user (admin).
     */
    public function users(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        return $this->ok(
            User::select('id', 'name', 'email', 'role', 'created_at')->orderBy('created_at', 'desc')->get(),
            'Daftar user'
        );
    }

    /**
     * Detail satu user beserta fitur yang aktif (admin).
     */
    public function showUser(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);
        $features = UserFeatureSetting::where('user_id', $id)->pluck('is_enabled', 'feature_key')->toArray();

        return $this->ok([
            'user' => $user,
            'features' => $features,
        ], 'Detail user');
    }

    /**
     * Update data user (admin).
     */
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);

        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,'.$id],
            'role' => ['sometimes', 'string', 'in:admin,member,finance,supervisor,pic,employee,internship'],
            'password' => ['sometimes', 'nullable', Password::min(8)],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $this->ok($user->fresh(), 'User berhasil diperbarui');
    }

    /**
     * Update fitur per-user (admin).
     */
    public function updateUserFeatures(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);

        User::findOrFail($id);

        $data = $request->validate([
            'features' => ['required', 'array'],
            'features.*.key' => ['required', 'string', 'exists:feature_settings,key'],
            'features.*.is_enabled' => ['required', 'integer', 'in:0,1'],
        ]);

        UserFeatureSetting::where('user_id', $id)->delete();

        foreach ($data['features'] as $f) {
            UserFeatureSetting::create([
                'user_id' => $id,
                'feature_key' => $f['key'],
                'is_enabled' => $f['is_enabled'],
            ]);
        }

        return $this->ok([], 'Fitur user diperbarui');
    }

    /**
     * Hapus user (admin). Tidak boleh menghapus diri sendiri.
     */
    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);

        if ($request->user()->id === $id) {
            return $this->fail('Tidak dapat menghapus akun sendiri.', 422);
        }

        $user = User::findOrFail($id);
        UserFeatureSetting::where('user_id', $id)->delete();
        $user->delete();

        return $this->ok([], 'User berhasil dihapus');
    }

    /**
     * Pastikan user adalah admin.
     */
    private function ensureAdmin(Request $request): void
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat mendaftarkan user.');
        }
    }
}
