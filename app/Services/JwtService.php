<?php
/**
 * MIRACLE SPA - Servicio de Autenticación con JWT (JSON Web Tokens)
 * Estándar RFC 7519 implementado con algoritmo HMAC-SHA256 (HS256)
 */

require_once __DIR__ . '/../../config/app.php';

class JwtService {
    // Clave secreta predeterminada (se recomienda sobreescribir con JWT_SECRET en .env)
    private const DEFAULT_SECRET = 'miracle_spa_sanctuary_jwt_secret_key_2026_x89a_secure!';
    private const DEFAULT_TTL_SECONDS = 86400; // 24 horas

    /**
     * Obtiene la clave secreta configurada
     */
    private static function getSecret(): string {
        return getenv('JWT_SECRET') ?: AppConfig::getSetting('jwt_secret', self::DEFAULT_SECRET);
    }

    /**
     * Codificación Base64Url segura
     */
    public static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodificación Base64Url
     */
    public static function base64UrlDecode(string $data): string {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padLen = 4 - $remainder;
            $data .= str_repeat('=', $padLen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Genera un token JWT firmado para un usuario o entidad
     */
    public static function generateToken(array $payloadData, ?int $ttlSeconds = null): string {
        $secret = self::getSecret();
        $ttl = $ttlSeconds ?? self::DEFAULT_TTL_SECONDS;
        $now = time();

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = array_merge([
            'iss' => AppConfig::getSetting('spa_name', 'Miracle Spa'),
            'iat' => $now,
            'exp' => $now + $ttl,
            'nbf' => $now
        ], $payloadData);

        $encodedHeader = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $encodedPayload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $signature = hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $secret, true);
        $encodedSignature = self::base64UrlEncode($signature);

        return "{$encodedHeader}.{$encodedPayload}.{$encodedSignature}";
    }

    /**
     * Valida y decodifica un token JWT
     */
    public static function validateToken(string $token): array {
        $parts = explode('.', trim($token));
        if (count($parts) !== 3) {
            return ['valid' => false, 'error' => 'Estructura de token JWT inválida (debe contener 3 segmentos).'];
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $headerJson = self::base64UrlDecode($encodedHeader);
        $payloadJson = self::base64UrlDecode($encodedPayload);

        $header = json_decode($headerJson, true);
        $payload = json_decode($payloadJson, true);

        if (!is_array($header) || !is_array($payload)) {
            return ['valid' => false, 'error' => 'Cabecera o cuerpo del token corruptos.'];
        }

        if (($header['alg'] ?? '') !== 'HS256') {
            return ['valid' => false, 'error' => 'Algoritmo no soportado. Se requiere HS256.'];
        }

        // Verificar firma criptográfica con comparación timing-safe
        $secret = self::getSecret();
        $expectedSignature = hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $secret, true);
        $providedSignature = self::base64UrlDecode($encodedSignature);

        if (!hash_equals($expectedSignature, $providedSignature)) {
            return ['valid' => false, 'error' => 'Firma digital inválida o token manipulado.'];
        }

        // Verificar expiración
        $now = time();
        if (isset($payload['exp']) && $now > $payload['exp']) {
            return [
                'valid' => false, 
                'error' => 'El token JWT ha expirado.', 
                'expired_at' => date('Y-m-d H:i:s', $payload['exp'])
            ];
        }

        // Verificar Not Before (nbf)
        if (isset($payload['nbf']) && $now < $payload['nbf']) {
            return ['valid' => false, 'error' => 'El token aún no es válido.'];
        }

        return [
            'valid' => true,
            'header' => $header,
            'payload' => $payload
        ];
    }

    /**
     * Extrae el token JWT de la petición HTTP actual
     * Soporta: Encabezado 'Authorization: Bearer <token>', Cookies o Parámetro POST/GET
     */
    public static function getTokenFromRequest(): ?string {
        // 1. Buscar en Authorization Header
        $authHeader = null;
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }

        if ($authHeader && preg_match('/Bearer\s+(.+)$/i', trim($authHeader), $matches)) {
            return trim($matches[1]);
        }

        // 2. Buscar en Cookie 'jwt_token'
        if (!empty($_COOKIE['jwt_token'])) {
            return trim($_COOKIE['jwt_token']);
        }

        // 3. Buscar en parámetro de petición
        if (!empty($_POST['jwt_token'])) {
            return trim($_POST['jwt_token']);
        }
        if (!empty($_GET['jwt_token'])) {
            return trim($_GET['jwt_token']);
        }

        return null;
    }
}
