<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\App;

class Helper
{
    public static function shout(string $string) {
        return strtoupper($string);
    }

    public static function cryptoJsAesDecrypt($passphrase, $jsonString) {
        $jsondata = json_decode($jsonString, true);
        try {
            $salt = hex2bin($jsondata["s"]);
            $iv  = hex2bin($jsondata["iv"]);
        }
        catch(Exception $e) {
            return null;
        }

        $ct = base64_decode($jsondata["ct"]);
        $concatedPassphrase = $passphrase.$salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }

    public static function composeReply($status, $msg, $payload, $httpStatusCode = 200, $returnAsJson = true,) { //LARAVEL WAY
        $arrReply = array(
            "SENDER" => "TALENTAKU",
            "STATUS" => $status,
            "MESSAGE" => $msg,
            "PAYLOAD" => $payload
        );

        if($returnAsJson) {
            $reply = json_encode($arrReply);
            return Response::make($reply, $httpStatusCode)->header('Content-Type', 'application/json');
        }
        else {
            return $arrReply;
        }
    }

    public static function composeReplyMessage($arrLocaleMessage) {
        foreach ($arrLocaleMessage as $key => $value) {
            if (App::getLocale() == $key) {
                return $value;
            }
        }
    }

    public static function insertLog($arrData) {
        $requiredFields = ['L_SESSION', 'L_CONTEXT', 'L_INFO'];

        foreach ($requiredFields as $field) {
            if (!isset($arrData[$field])) {
                throw new \Exception("Field {$field} is required.");
            }
        }

        $insertData = [
            'L_SESSION' => $arrData['L_SESSION'],
            'L_DATE' => now()->format('Y-m-d H:i:s'),
            'L_CONTEXT' => $arrData['L_CONTEXT'],
            'L_INFO' => $arrData['L_INFO'],
            'L_SUBJECT_U_ID' => $arrData['L_SUBJECT_U_ID'] ?? '-',
            'L_REMOTE_ADDRESS' => $arrData['L_REMOTE_ADDRESS'] ?? '-',
        ];

        DB::table('_logs')->insert($insertData);
    }

    public static function dayDifference2($dateA, $dateB, $inDaysOnly = false) {
        $date1 = new DateTime($dateA);
        $date2 = new DateTime($dateB);
        $interval = $date1->diff($date2);

        if($inDaysOnly == TRUE) {
            // shows the total amount of days (not divided into years, months and days like above)
            return $interval->d;
        }
        else {
            $arrDiff["DAY"]     = $interval->d;
            $arrDiff["MONTH"]   = $interval->m;
            $arrDiff["YEAR"]    = $interval->y;
            $arrDiff["HOUR"]    = $interval->h;
            $arrDiff["MINUTE"]  = $interval->i;
            $arrDiff["SECOND"]  = $interval->s;
            return $arrDiff;
        }
    }

    public static function distance($lat1, $lng1, $lat2, $lng2, $unit = "K") {
        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        }
        else if ($unit == "N") {
            return ($miles * 0.8684);
        }
        else {
            return $miles;
        }
    }

    public static function getCoordinateProximity($lat, $lng, $asNearestSingleObject = true) {
        $jarak = DB::select("SELECT csm_climate_location.*, ST_Distance_Sphere(
            POINT(csm_climate_location.center_lng, csm_climate_location.center_lat),
            POINT(?, ?)
        )/1000 as jarak_km
        FROM csm_climate_location
        WHERE csm_climate_location.center_lat != '0' AND csm_climate_location.center_lng != '0'
        ORDER BY jarak_km ASC", array($lng, $lat));

        if(count($jarak) > 0) {
            if($asNearestSingleObject) {
                return $jarak[0];
            }
            else {
                return $jarak;
            }
        }
        else {
            return false;
        }
    }

    public static function getAllSetting()
    {
        $allowedIds = [
            'APP_NAME',
            'CLIENT_NAME',
            'CONVERT_API_KEY',
            'DEFAULT_PASSWORD',
            'LOGIN_EXPIRED_DAYS',
            'BILLING_FOOT_NOTE',
        ];

        return DB::table('_settings')
            ->select('SET_ID', 'SET_VALUE', 'SET_INFO')
            ->whereIn('SET_ID', $allowedIds)
            ->get();
    }

    public static function getSetting($setId)  {
        $setValue = "";
        $setting = DB::select("SELECT SET_VALUE FROM _settings WHERE SET_ID = ?",array($setId));
        if(!empty($setting)) {
            $rs_setting = $setting[0];
            $setValue = $rs_setting->{"SET_VALUE"};
        }

        return $setValue;
    }

    public static function saveSetting($setId, $setValue) {
        DB::table("_settings")
            ->where("SET_ID", $setId)
            ->update(array(
                "SET_VALUE" => $setValue
            ));
    }

    public static function getReferenceInfo($rCategory, $rId) {
        $ref = DB::table("_references")
            ->where("R_CATEGORY",$rCategory)
            ->where("R_ID",$rId)
            ->first();

        if(isset($ref->{"R_INFO"})) {
            return $ref->{"R_INFO"};
        }
        else {
            return "";
        }
    }

    public static function getReferencesByCategory($rCategory) {
        $refs = DB::table("_references")
            ->where("R_CATEGORY", $rCategory)
            ->orderBy("R_ORDER", "asc")
            ->get();

        return $refs;
    }

    public static function isReferenceIdValid($rCategory, $rId) {
        $ref = DB::table("_references")
            ->where("R_CATEGORY",$rCategory)
            ->where("R_ID",$rId)
            ->first();

        if($ref) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function getEnumValues($table, $field) {
        $type = DB::select( "SHOW COLUMNS FROM ".$table." WHERE Field = ?",[$field]);
        //die("type : ".json_encode($type));
        preg_match("/^enum\(\'(.*)\'\)$/", $type[0]->{"Type"}, $matches);
        $enum = explode("','", $matches[1]);

        return $enum;
    }

    public static function randomDigits($length = 5){
        $digits = "";
        $numbers = range(0,9);
        shuffle($numbers);
        for($i = 0;$i < $length;$i++) {
            $digits .= $numbers[$i];
        }
        return $digits;
    }

    public static function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function createSlug($string, $withCode = 0){
        $replace = '-';
        $string = strtolower($string);

        //replace / and . with white space
        $string = preg_replace("/[\/\.]/", " ", $string);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);

        //remove multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);

        //convert whitespaces and underscore to $replace
        $string = preg_replace("/[\s_]/", $replace, $string);

        //$string = $string."-".substr(md5(date("Y-m-d H:i:s")),0,4);

        //limit the slug size
        $string = substr($string, 0, 100);

        //slug is generated
        if(isset($withCode)) {
            if(intval($withCode) > 0 && intval($withCode) <= 7)  {
                return $string."-".createCode($withCode);
            }
            else {
                return $string;
            }
        }
        else {
            return $string;
        }
    }

    public static function customArrayMerge(&$array1, &$array2) {
        $result = [];
        foreach ($array1 as $key_1 => &$value_1) {
            foreach ($array2 as $key_2 => $value_2) {
                if($value_1 ==  $value_2) {
                    $result[] = array_merge($value_1, $value_2);
                }
            }
        }
        return $result;
    }

    public static function formatPonsel($ponsel, $prefix) {
        if(trim($ponsel) != "" && trim($ponsel) != "-") {
            if(substr($ponsel,0,5) == "+6262" || substr($ponsel,0,4) == "+620" || substr($ponsel,0,4) == "6262" || substr($ponsel,0,3) == "620") {
                //+626281xxxx
                if(substr($ponsel,0,5) == "+6262")  {
                    if($prefix == "+62") {
                        $ponsel = "+62".substr($ponsel,5);
                    }
                    if($prefix == "0") {
                        $ponsel = "0".substr($ponsel,5);
                    }
                    if(trim($prefix) == "") {
                        $ponsel = substr($ponsel,5);
                    }
                }

                //+62081xxxx
                if(substr($ponsel,0,4) == "+620") {
                    if($prefix == "+62") {
                        $ponsel = "+62".substr($ponsel,4);
                    }
                    if($prefix == "0") {
                        $ponsel = "0".substr($ponsel,4);
                    }
                    if(trim($prefix) == "") {
                        $ponsel = substr($ponsel,4);
                    }
                }

                //626281xxxx
                if(substr($ponsel,0,4) == "6262") {
                    if($prefix == "+62") {
                        $ponsel = "+62".substr($ponsel,4);
                    }
                    if($prefix == "0") {
                        $ponsel = "0".substr($ponsel,4);
                    }
                    if(trim($prefix) == "") {
                        $ponsel = substr($ponsel,4);
                    }
                }

                //62081xxxx
                if(substr($ponsel,0,3) == "620")  {
                    if($prefix == "+62") { //no change
                        $ponsel = "+62".substr($ponsel,3);
                    }
                    if($prefix === "0") {
                        $ponsel = "0".substr($ponsel,3);
                    }
                    if(trim($prefix) == "") {
                        $ponsel = substr($ponsel,3);
                    }
                }
            }
            else {
                //+6281xxxxx
                if(substr($ponsel,0,3) == "+62")  {
                    if($prefix == "+62") { //no change

                    }
                    if($prefix == "0") {
                        $ponsel = "0".substr($ponsel,3);
                    }
                    if(trim($prefix) == "") {
                        $ponsel = substr($ponsel,3);
                    }
                }

                //628xxxxx
                if(substr($ponsel,0,2) == "62") {
                    if($prefix == "+62") {
                        $ponsel = "+".$ponsel;
                    }
                    if($prefix == "0") {
                        $ponsel = "0".substr($ponsel,2);
                    }
                    if(trim($prefix) == "") {
                        $ponsel = substr($ponsel,2);
                    }
                }

                //8132333
                if(substr($ponsel,0,1) == "8")  {
                    if($prefix == "+62") {
                        $ponsel = "+62".$ponsel;
                    }
                    if($prefix == "0") {
                        $ponsel = "0".$ponsel;
                    }
                    if(trim($prefix) == "") { //no change

                    }
                }

                //081xxxxx
                if(substr($ponsel,0,2) == "08") {
                    if($prefix == "+62") {
                        $ponsel = "+62".substr($ponsel,1);
                    }
                    if($prefix == "0") { //no change

                    }
                    if(trim($prefix) == "") {
                        $ponsel = substr($ponsel,1);
                    }
                }
            }
        }

        return $ponsel;
    }

    public static function tglIndo($tgl, $mode = "SHORT") {
        if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00") {
            $t = explode("-",$tgl);
            $bln = array();
            $bln["01"]["LONG"] = "Januari";
            $bln["01"]["SHORT"] = "Jan";
            $bln["1"]["LONG"] = "Januari";
            $bln["1"]["SHORT"] = "Jan";
            $bln["02"]["LONG"] = "Februari";
            $bln["02"]["SHORT"] = "Feb";
            $bln["2"]["LONG"] = "Februari";
            $bln["2"]["SHORT"] = "Feb";
            $bln["03"]["LONG"] = "Maret";
            $bln["03"]["SHORT"] = "Mar";
            $bln["3"]["LONG"] = "Maret";
            $bln["3"]["SHORT"] = "Mar";
            $bln["04"]["LONG"] = "April";
            $bln["04"]["SHORT"] = "Apr";
            $bln["4"]["LONG"] = "April";
            $bln["4"]["SHORT"] = "Apr";
            $bln["05"]["LONG"] = "Mei";
            $bln["05"]["SHORT"] = "Mei";
            $bln["5"]["LONG"] = "Mei";
            $bln["5"]["SHORT"] = "Mei";
            $bln["06"]["LONG"] = "Juni";
            $bln["06"]["SHORT"] = "Jun";
            $bln["6"]["LONG"] = "Juni";
            $bln["6"]["SHORT"] = "Jun";
            $bln["07"]["LONG"] = "Juli";
            $bln["07"]["SHORT"] = "Jul";
            $bln["7"]["LONG"] = "Juli";
            $bln["7"]["SHORT"] = "Jul";
            $bln["08"]["LONG"] = "Agustus";
            $bln["08"]["SHORT"] = "Ags";
            $bln["8"]["LONG"] = "Agustus";
            $bln["8"]["SHORT"] = "Ags";
            $bln["09"]["LONG"] = "September";
            $bln["09"]["SHORT"] = "Sep";
            $bln["9"]["LONG"] = "September";
            $bln["9"]["SHORT"] = "Sep";
            $bln["10"]["LONG"] = "Oktober";
            $bln["10"]["SHORT"] = "Okt";
            $bln["11"]["LONG"] = "November";
            $bln["11"]["SHORT"] = "Nov";
            $bln["12"]["LONG"] = "Desember";
            $bln["12"]["SHORT"] = "Des";

            $b = $t[1];

            if (strpos($t[2], ":") === false) { //tdk ada format waktu
                $jam = "";
            }
            else {
                $j = explode(" ",$t[2]);
                $t[2] = $j[0];
                $jam = $j[1];
            }

            return $t[2]." ".$bln[$b][$mode]." ".$t[0]." ".$jam;
        }
        else {
            return "-";
        }
    }

    public static function bulanIndo($b, $mode = "SHORT") {
        $bln["01"]["LONG"] = "Januari";
        $bln["01"]["SHORT"] = "Jan";
        $bln["1"]["LONG"] = "Januari";
        $bln["1"]["SHORT"] = "Jan";
        $bln["02"]["LONG"] = "Februari";
        $bln["02"]["SHORT"] = "Feb";
        $bln["2"]["LONG"] = "Februari";
        $bln["2"]["SHORT"] = "Feb";
        $bln["03"]["LONG"] = "Maret";
        $bln["03"]["SHORT"] = "Mar";
        $bln["3"]["LONG"] = "Maret";
        $bln["3"]["SHORT"] = "Mar";
        $bln["04"]["LONG"] = "April";
        $bln["04"]["SHORT"] = "Apr";
        $bln["4"]["LONG"] = "April";
        $bln["4"]["SHORT"] = "Apr";
        $bln["05"]["LONG"] = "Mei";
        $bln["05"]["SHORT"] = "Mei";
        $bln["5"]["LONG"] = "Mei";
        $bln["5"]["SHORT"] = "Mei";
        $bln["06"]["LONG"] = "Juni";
        $bln["06"]["SHORT"] = "Jun";
        $bln["6"]["LONG"] = "Juni";
        $bln["6"]["SHORT"] = "Jun";
        $bln["07"]["LONG"] = "Juli";
        $bln["07"]["SHORT"] = "Jul";
        $bln["7"]["LONG"] = "Juli";
        $bln["7"]["SHORT"] = "Jul";
        $bln["08"]["LONG"] = "Agustus";
        $bln["08"]["SHORT"] = "Ags";
        $bln["8"]["LONG"] = "Agustus";
        $bln["8"]["SHORT"] = "Ags";
        $bln["09"]["LONG"] = "September";
        $bln["09"]["SHORT"] = "Sep";
        $bln["9"]["LONG"] = "September";
        $bln["9"]["SHORT"] = "Sep";
        $bln["10"]["LONG"] = "Oktober";
        $bln["10"]["SHORT"] = "Okt";
        $bln["11"]["LONG"] = "November";
        $bln["11"]["SHORT"] = "Nov";
        $bln["12"]["LONG"] = "Desember";
        $bln["12"]["SHORT"] = "Des";

        return $bln[$b][$mode];
    }

    public static function tglInggris($tgl, $mode = "SHORT") {
        if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00" && $tgl != "-") {
            $t = explode("-",$tgl);
            $bln = array();
            $bln["01"]["LONG"] = "January";
            $bln["01"]["SHORT"] = "Jan";
            $bln["1"]["LONG"] = "January";
            $bln["1"]["SHORT"] = "Jan";
            $bln["02"]["LONG"] = "February";
            $bln["02"]["SHORT"] = "Feb";
            $bln["2"]["LONG"] = "February";
            $bln["2"]["SHORT"] = "Feb";
            $bln["03"]["LONG"] = "March";
            $bln["03"]["SHORT"] = "Mar";
            $bln["3"]["LONG"] = "March";
            $bln["3"]["SHORT"] = "Mar";
            $bln["04"]["LONG"] = "April";
            $bln["04"]["SHORT"] = "Apr";
            $bln["4"]["LONG"] = "April";
            $bln["4"]["SHORT"] = "Apr";
            $bln["05"]["LONG"] = "May";
            $bln["05"]["SHORT"] = "May";
            $bln["5"]["LONG"] = "May";
            $bln["5"]["SHORT"] = "May";
            $bln["06"]["LONG"] = "June";
            $bln["06"]["SHORT"] = "Jun";
            $bln["6"]["LONG"] = "June";
            $bln["6"]["SHORT"] = "Jun";
            $bln["07"]["LONG"] = "July";
            $bln["07"]["SHORT"] = "Jul";
            $bln["7"]["LONG"] = "July";
            $bln["7"]["SHORT"] = "Jul";
            $bln["08"]["LONG"] = "August";
            $bln["08"]["SHORT"] = "Aug";
            $bln["8"]["LONG"] = "August";
            $bln["8"]["SHORT"] = "Aug";
            $bln["09"]["LONG"] = "September";
            $bln["09"]["SHORT"] = "Sep";
            $bln["9"]["LONG"] = "September";
            $bln["9"]["SHORT"] = "Sep";
            $bln["10"]["LONG"] = "October";
            $bln["10"]["SHORT"] = "Oct";
            $bln["11"]["LONG"] = "November";
            $bln["11"]["SHORT"] = "Nov";
            $bln["12"]["LONG"] = "December";
            $bln["12"]["SHORT"] = "Dec";

            $b = $t[1];

            if (strpos($t[2], ":") === false) { //tdk ada format waktu
                $jam = "";
            }
            else {
                $j = explode(" ",$t[2]);
                $t[2] = $j[0];
                $jam = $j[1];
            }

            return $t[2]." ".$bln[$b][$mode]." ".$t[0]." ".$jam;
        }
        else {
            return "-";
        }
    }

    public static function blnInggris($aBln, $mode = "SHORT") {
        $bln = array();
        $bln["01"]["LONG"] = "January";
        $bln["01"]["SHORT"] = "Jan";
        $bln["1"]["LONG"] = "January";
        $bln["1"]["SHORT"] = "Jan";
        $bln["02"]["LONG"] = "February";
        $bln["02"]["SHORT"] = "Feb";
        $bln["2"]["LONG"] = "February";
        $bln["2"]["SHORT"] = "Feb";
        $bln["03"]["LONG"] = "March";
        $bln["03"]["SHORT"] = "Mar";
        $bln["3"]["LONG"] = "March";
        $bln["3"]["SHORT"] = "Mar";
        $bln["04"]["LONG"] = "April";
        $bln["04"]["SHORT"] = "Apr";
        $bln["4"]["LONG"] = "April";
        $bln["4"]["SHORT"] = "Apr";
        $bln["05"]["LONG"] = "May";
        $bln["05"]["SHORT"] = "May";
        $bln["5"]["LONG"] = "May";
        $bln["5"]["SHORT"] = "May";
        $bln["06"]["LONG"] = "June";
        $bln["06"]["SHORT"] = "Jun";
        $bln["6"]["LONG"] = "June";
        $bln["6"]["SHORT"] = "Jun";
        $bln["07"]["LONG"] = "July";
        $bln["07"]["SHORT"] = "Jul";
        $bln["7"]["LONG"] = "July";
        $bln["7"]["SHORT"] = "Jul";
        $bln["08"]["LONG"] = "August";
        $bln["08"]["SHORT"] = "Ags";
        $bln["8"]["LONG"] = "August";
        $bln["8"]["SHORT"] = "Ags";
        $bln["09"]["LONG"] = "September";
        $bln["09"]["SHORT"] = "Sep";
        $bln["9"]["LONG"] = "September";
        $bln["9"]["SHORT"] = "Sep";
        $bln["10"]["LONG"] = "October";
        $bln["10"]["SHORT"] = "Oct";
        $bln["11"]["LONG"] = "November";
        $bln["11"]["SHORT"] = "Nov";
        $bln["12"]["LONG"] = "December";
        $bln["12"]["SHORT"] = "Des";

        return $bln[$aBln][$mode];
    }

    public static function isUserOrGroupHasAccess($userId, $moduleId, $accessType = "READ") {
        if(strtoupper($accessType) == "READ") {
            $fieldFlag = "ACC_FLAG_READ";
        }
        if(strtoupper($accessType) == "UPDATE") {
            $fieldFlag = "ACC_FLAG_UPDATE";
        }
        if(strtoupper($accessType) == "CREATE") {
            $fieldFlag = "ACC_FLAG_CREATE";
        }
        if(strtoupper($accessType) == "DELETE") {
            $fieldFlag = "ACC_FLAG_DELETE";
        }

        if(!isset($fieldFlag))  return false;

        $x = DB::table("_modules_access")
            ->join("_users_groups", "_modules_access.GROUP_ROLE", "=", "_users_groups.GROUP_ROLE")
            ->where("_users_groups.U_ID", $userId)
            ->where("_modules_access.".$fieldFlag, "Y");

        if(is_array($moduleId) && count($moduleId) > 0) {
            $x->whereIn("_modules_access.MODULE_ID", $moduleId);
        }
        else {
            $x->where("_modules_access.MODULE_ID", $moduleId);
        }

        $group = $x->get();

        Log::info($group);
        Log::info($userId);

        if(count($group) == 0) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function isJSON($string){
        json_decode($string);
        return json_last_error() == JSON_ERROR_NONE;
    }

    public static function getFirebaseAPIKey() {
        $apiKey = Helper::getSetting("FIREBASE_API_KEY");
        if(trim($apiKey) == "" || strlen($apiKey) < 10) $apiKey = "AAAA6B2NV-s:APA91bEZl7I1d5i_Q9PtW1d10kVA5gsGFyLYq5-cBPyqQc1lPaO-OYcgFlP791oN8pqO0XOYRNytX2zrW-35c0NfzEaNgasgJyvNZzNiMBI-wnfPVoapzGnuEQXiiurnavvAm2VmMavp";

        return $apiKey;
    }

    public static function sendAndroidPushNotification($registrationId, $notificationTitle, $notificationBody, $payloadTitle, $payloadMessage, $arrAdditionalData, $writeOutbox = true) {
        if(trim($registrationId) == ""  || trim($registrationId) == "-")    return "Invalid device ID";

        //https://stackoverflow.com/questions/37711082/how-to-handle-notification-when-app-in-background-in-firebase
        /*
        field "notification" by default dibaca dulu ketika app in foreground, meskipun ada "data" (mengabaikan "data")
        ketika di background, "notification" tidak dibaca dan skip lsg ke "data"
        => kecuali jika ada "notification.click_action", maka "notification" yg akan dibaca dulu meski app di background
        pd kasus ada "click_action", akan lari ke intent yg dituju, dgn parameter dinamis yg ada di field "data"

        => KESIMPULAN (SEMENTARA) : handle push notif di foreground dan background bisa pake "notification" asal ada "click_action" dan field "data"
        */

        /*
        field "data" akan dibaca ketika app in background
        namun ketika di foreground DAN tidak ada "notification" (bisa sengaja diremove), field "data" yg akan dibaca
        */

        $arrData["title"] = $payloadTitle;
        $arrData["message"] = $payloadMessage;
        if(isset($arrAdditionalData) && count($arrAdditionalData) > 0) {
            foreach ($arrAdditionalData as $key => $value) {
                $arrData[$key] = $value;
            }
        }

        //$arrNotif["click_action"] = ".activity.Main2Activity";
        $arrNotif["title"] = $payloadTitle;
        $arrNotif["body"] = $payloadMessage;

        $data = array(
            "to" => $registrationId,
            //"notification" sengaja diremove (meskipun ada "click_action", itu sengaja biar ada sbg reminder) supaya lsg diproses ke "data"
            /*
            "notification" => array(
                "click_action" => ".activity.MainActivity",
                "title" => $notificationTitle,
                "body" => $notificationBody
            ),
            */
            //contoh "data"
            /*
            "data" => array(
                "title" => $payloadTitle,
                "message" => $payloadMessage
            )
            */
            "data" => $arrData,
            "notification" => $arrNotif,
            "content-available" => true,
            "priority" => "high"
        );
        $dataEncoded = json_encode($data);

        $headers = array(
            'Authorization: key='.Helper::getFirebaseAPIKey(),
            'Content-Type: application/json'
        );

        $ch = curl_init();

        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, $dataEncoded);

        $result = curl_exec($ch);

        curl_close ($ch);

        if($writeOutbox) {
            $userData = DB::table("_users")
                ->where("U_FCM_TOKEN", $registrationId)
                ->first();

            $outId = DB::table("csm_outbox")->insertGetId(array(
                "OUT_DATE_SEND" => date("Y-m-d H:i:s"),
                "OUT_METHOD" => "OUT_PUSH_NOTIFICATION",
                "OUT_DESTINATION" => $registrationId,
                "OUT_RECIPIENT" => ($userData ? $userData->{"U_ID"}."/".$userData->{"U_NAME"} : "Unknown user"),
                "OUT_CONTENT" => /*$notificationTitle." : ".*/$notificationBody,
                "OUT_PROVIDER_RESPONSE" => "Authorization: key=".Helper::getFirebaseAPIKey()." => ".$result
            ));
        }

        return $result;
    }

    public static function sendTelegram($msg) {
        $botToken = '704728468:AAHd7pun6Z4f5UyjiMNnvkfqUSvPm94QwBY';
        $website="https://api.telegram.org/bot".$botToken;
        $chatID='-227726717';  //Receiver Chat Id
        $send = '';

        $send .= " -- ESTETIKA --\n\n";
        $send .= $msg."\n";

        $params=[
            'chat_id' => $chatID,
            'text' => $send,
            'parse_mode' => 'HTML'
        ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function base64ToFile($base64String, $outputFile) {
        // open the output file for writing
        $ifp = fopen( $outputFile, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        if(strpos($base64String, "data:image/png;base64") !== false) {
            $data = explode( ',', $base64String );
            $base64String = $data[1];
        }

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $base64String ) );

        // clean up the file resource
        fclose( $ifp );

        return $outputFile;
    }

    public static function getMimeTypeFromBase64($base64Data)
    {
        if (preg_match('/^data:(.*?);base64,/', $base64Data, $matches)) {
            return $matches[1];
        }

        return 'application/octet-stream';
    }

    public static function removeBase64Header($base64Data)
    {
        return preg_replace('/^data:\w+\/[\w\-]+;base64,/', '', $base64Data);
    }

    public static function arrayChangeKeyCaseRecursive(array $array) {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = strtoupper($key);
            if (is_array($value)) {
                $result[$newKey] = self::arrayChangeKeyCaseRecursive($value);
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }
}
