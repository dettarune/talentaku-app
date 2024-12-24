<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ApiImageController
{
    protected $userData;

    public function __construct(Request $request,  )
    {
        $this->userData = $request->{"USER_DATA"};
    }
    public function getImageByID(Request $request)
    {
        try {
            $ID = $request->query('url');
            log::info('L_SESSION'. session()->getId());
            log::info('L_CONTEXT'. __METHOD__,);
            log::info('L_SUBJECT_U_ID' . $this->userData->{"U_ID"} ?? '-');
            Helper::insertLog([
                'L_SESSION' => session()->getId(),
                'L_CONTEXT' => __METHOD__,
                'L_INFO' => "Memulai permintaan pengambilan gambar untuk ID: {$ID}",
                'L_SUBJECT_U_ID' => $this->userData->{"U_ID"} ?? '-',
                'L_REMOTE_ADDRESS' => request()->ip(),
            ]);
            if (!isset($ID)) {
                Helper::insertLog([
                    'L_SESSION' => session()->getId(),
                    'L_CONTEXT' => __METHOD__,
                    'L_INFO' => "Gagal mengambil gambar. Parameter ID tidak diberikan.",
                    'L_SUBJECT_U_ID' => $this->userData->{"U_ID"} ?? '-',
                    'L_REMOTE_ADDRESS' => request()->ip(),
                ]);
                return Helper::composeReply("ERROR", trans("generic.EmptyId"), null);
            }
            $imgBase64 = DB::table('_medias')
                ->select('MEDIA_MIME_TYPE', 'MEDIA_CONTENT_VALUE')
                ->where('MEDIA_ID',$ID )
                ->where("MEDIA_CONTENT_TYPE", "=", "Base64")
                ->first();
            if (!$imgBase64) {
                Helper::insertLog([
                    'L_SESSION' => session()->getId(),
                    'L_CONTEXT' => __METHOD__,
                    'L_INFO' => "Gambar tidak ditemukan untuk ID: {$ID}, menggunakan gambar default.",
                    'L_SUBJECT_U_ID' => $this->userData->{"U_ID"} ?? '-',
                    'L_REMOTE_ADDRESS' => request()->ip(),
                ]);
                $imageData = 'asdffa';
                return Response::make($imageData, 200, [
                    'Content-Type' => 'image/png',
                    'Content-Length' => strlen($imageData)
                ]);
            }
            Helper::insertLog([
                'L_SESSION' => session()->getId(),
                'L_CONTEXT' => __METHOD__,
                'L_INFO' => "Berhasil mengambil gambar untuk ID: {$ID}",
                'L_SUBJECT_U_ID' => $this->userData->{"U_ID"} ?? '-',
                'L_REMOTE_ADDRESS' => request()->ip(),
            ]);
            $imageData = base64_decode($imgBase64->{"MEDIA_CONTENT_VALUE"});
            return Response::make($imageData, 200, [
                'Content-Type' => $imgBase64->{"MEDIA_MIME_TYPE"},
                'Content-Length' => strlen($imageData),
            ]);
        } catch (\Exception $e) {
            Helper::insertLog([
                'L_SESSION' => session()->getId(),
                'L_CONTEXT' => __METHOD__,
                'L_INFO' => "Terjadi kesalahan pada fungsi getStudentImageByStudentCode: " . $e->getMessage(),
                'L_SUBJECT_U_ID' => $this->userData->{"U_ID"} ?? '-',
                'L_REMOTE_ADDRESS' => request()->ip(),
            ]);
            return Helper::composeReply("ERROR", $e->getMessage(), null);
        }
    }
}
