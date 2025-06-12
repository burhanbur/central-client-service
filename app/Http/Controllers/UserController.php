<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;

use Exception;

class UserController extends Controller
{
    use ApiResponse;

    public function me(Request $request)
    {
        try {
            $data = auth()->user();

            return $this->successResponse(
                new UserResource($data), 
                'Data pengguna berhasil diambil.'
            );
        } catch (Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }
}
