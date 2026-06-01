<?php

// app/Libraries/WhatsAppMessageSender.php

namespace App\Libraries;

class WhatsAppSender
{
    public static function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '127.0.0.1';
        }
        return $ip;
    }

    public static function getCityAndCountry($ip)
    {
       $url = "http://ip-api.com/json/$ip";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        
        if(curl_errno($ch)){
            // Handle curl error
            $error_message = curl_error($ch);
            curl_close($ch);
            return array("city" => "Unknown", "country" => "Unknown", "error" => $error_message);
        }
        
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if ($data['status'] == 'success') {
            return array("city" => $data['city'], "country" => $data['country']);
        } else {
            return array("city" => "Unknown", "country" => "Unknown");
        }

    }

    public static function getDeviceInfo()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        $devices = array(
            "Mobile" => "/(tablet|mobile|iphone|ipod|android|windows phone)/i",
            "Tablet" => "/(tablet|ipad|android(?!.*mobile))/i",
            "Desktop" => "/(windows|macintosh|linux)/i"
        );

        $os = "";
        $device = "Unknown";

        foreach ($devices as $type => $regex) {
            if (preg_match($regex, $user_agent)) {
                $device = $type;
                break;
            }
        }

        if (strpos($user_agent, "Windows NT") !== false) {
            $os = "Windows";
        } elseif (strpos($user_agent, "Macintosh") !== false) {
            $os = "Macintosh";
        } elseif (strpos($user_agent, "Android") !== false) {
            $os = "Android";
        } elseif (strpos($user_agent, "iPhone") !== false) {
            $os = "iPhone";
        } elseif (strpos($user_agent, "iPad") !== false) {
            $os = "iPad";
        } elseif (strpos($user_agent, "Linux") !== false) {
            $os = "Linux";
        }

        return array("device" => $device, "os" => $os);
    }

    public static function sendMessage($recipientNumber, $email)
    {
        $ip = self::getUserIpAddr();
        $location = self::getCityAndCountry($ip);
        $deviceInfo = self::getDeviceInfo();

        $type = 'text';
        $appKey = '15443ea7-3867-4e35-8743-d884cfbc28d9';
        $authKey = 'GnnmSafT3XzVUGmTdvMJlEoZ32L7Rzvvjrjlpd8d1YHYnlQzU7';

        $message = $message = urlencode("As salam O alaikum!\nIt is *Logged IN* User Information\nfrom *https://fgfps.com/software*\n$email  Logged in at " . date('d-m-Y H:i:s') . "\n$email  Real IP - " . $ip . "\nURL: https://fgfps.com/software/\n$email  City: " . $location['city'] . "\n$email  Country: " . $location['country'] . "\n$email  Device: " . $deviceInfo['device'] . "\n$email  Operating System: " . $deviceInfo['os'] . "\n\nRegards *SmartestDevelopers*
            ");


        $apiUrl = "https://wa.net.pk/api2/send.php?number=$recipientNumber&type=$type&message=$message&appkey=$appKey&authkey=$authKey";

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return 'cURL error: ' . curl_error($ch);
        } else {
            return 'API Response: ' . $response;
        }

        curl_close($ch);
    }
}
