<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponse;

class CentralAuthenticate
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        $token = null;
        $isCookie = false;

        // Cek Authorization header (Bearer)
        if ($request->bearerToken()) {
            $token = $request->bearerToken();
        }

        // Kalau tidak ada, cek cookie
        if (!$token && $request->hasCookie(config('central.cookie_name'))) {
            $token = $request->cookie(config('central.cookie_name'));
            $isCookie = true;
        }
        
        // Jika tidak ada token sama sekali, return unauthorized
        if (!$token) {
            return $this->errorResponse('Token tidak ditemukan, harap login terlebih dahulu.', 401);
        }

        // Check session dengan Central API
        $headers = [
            'x-app-id: ' . config('central.client_app_id'),
            'Authorization: Bearer ' . $token
        ];
        $url = config('central.api_url') . '/api/v1/client/session';
        $response = postCurl($url, null, $headers);

        // Jika response tidak sukses, return unauthorized
        if (!$response || !isset($response->success) || $response->success !== true) {
            return $this->errorResponse('Token tidak valid atau sudah kadaluarsa.', 401);
        }

        // Attribute `code` disesuaikan dengan attribute `NIP` pada masing-masing database user di aplikasi client
        $user = User::where('code', $response->data->user->code)->first();

        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan.', 401);
        }

        auth()->setUser($user);

        // Jika semua validasi berhasil, lanjutkan ke request berikutnya
        return $next($request);
    }
}