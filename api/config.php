<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'test');

// JWT Configuration
define('JWT_SECRET_KEY', 'cf5f2509fbe89a3c1dd8c2211c59781c8b878bbcea45d958bfcc70d2959d0b47273f226d9d84f58e616b064f53e0edfab007a766c6a1ceb2e70098b08db2cb0a');
define('JWT_EXPIRE_TIME', 3600 * 24 * 30); // Token expiry time 30 days

class JWT {
    private static $algorithms = ['HS256'];
    
    public static function encode($payload, $key) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $header = self::base64UrlEncode($header);
        
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRE_TIME;
        $payload = json_encode($payload);
        $payload = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', "$header.$payload", $key, true);
        $signature = self::base64UrlEncode($signature);
        
        return "$header.$payload.$signature";
    }
    
    public static function decode($token, $key) {
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        $valid = hash_hmac('sha256', "$header.$payload", $key, true);
        $valid = self::base64UrlEncode($valid);
        
        if ($signature !== $valid) {
            return false;
        }
        
        $payload = json_decode(self::base64UrlDecode($payload), true);
        
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
?> 