<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ApiImageController
{
    protected $userData;

    public function __construct(Request $request,  )
    {
        $this->userData = $request->{"USER_DATA"};
    }
    public function getUserProfileImage($url)
    {
        $filePath = $url;
        if (!Storage::disk('public')->exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $fileContent = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);
        return response($fileContent, 200)->header('Content-Type', $mimeType);
    }

    public function getStudentProfileImage($url)
    {
        $filePath = urldecode($url);
        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $fileContent = Storage::get($filePath);
        $mimeType = Storage::mimeType($filePath);
        return response($fileContent, 200)->header('Content-Type', $mimeType);
    }
}
