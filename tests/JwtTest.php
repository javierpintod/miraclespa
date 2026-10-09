<?php
/**
 * MIRACLE SPA - Suite de Pruebas Unitarias para Autenticación con JWT (RFC 7519)
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Services/JwtService.php';

class JwtTest {
    private array $results = [];

    public function runAll(): array {
        $this->testGenerateTokenStructure();
        $this->testValidateAuthenticToken();
        $this->testRejectTamperedTokenPayload();
        $this->testRejectExpiredToken();
        $this->testBase64UrlEncodingSafety();
        return $this->results;
    }

    private function assert(string $testName, bool $condition, string $message = ''): void {
        $this->results[] = [
            'suite' => 'Autenticación con JWT (JSON Web Tokens)',
            'test' => $testName,
            'passed' => $condition,
            'message' => $message
        ];
    }

    private function testGenerateTokenStructure(): void {
        $token = JwtService::generateToken([
            'sub' => 1,
            'username' => 'admin',
            'role' => 'admin'
        ]);

        $parts = explode('.', $token);
        $this->assert('El token JWT debe estar estructurado en exactamente 3 segmentos (Header.Payload.Signature)', count($parts) === 3);

        $header = json_decode(JwtService::base64UrlDecode($parts[0]), true);
        $this->assert('La cabecera debe declarar el algoritmo HS256 y tipo JWT', ($header['alg'] ?? '') === 'HS256' && ($header['typ'] ?? '') === 'JWT');
    }

    private function testValidateAuthenticToken(): void {
        $token = JwtService::generateToken([
            'sub' => 42,
            'username' => 'terapeuta_vip',
            'name' => 'Camila Morales',
            'role' => 'recepcionista'
        ], 3600);

        $val = JwtService::validateToken($token);
        $this->assert('Token legítimo recién generado debe ser validado como VÁLIDO', $val['valid'] === true);
        $this->assert('El payload decodificado debe recuperar los claims originales del usuario', ($val['payload']['sub'] ?? 0) === 42 && ($val['payload']['username'] ?? '') === 'terapeuta_vip');
    }

    private function testRejectTamperedTokenPayload(): void {
        $token = JwtService::generateToken(['sub' => 1, 'role' => 'recepcionista']);
        $parts = explode('.', $token);

        // Modificar maliciosamente el payload (elevar a admin)
        $tamperedPayload = json_encode(['sub' => 1, 'role' => 'superadmin']);
        $fakeEncodedPayload = JwtService::base64UrlEncode($tamperedPayload);
        $tamperedToken = "{$parts[0]}.{$fakeEncodedPayload}.{$parts[2]}";

        $val = JwtService::validateToken($tamperedToken);
        $this->assert('Token con payload alterado debe ser RECHAZADO por fallo de firma digital', $val['valid'] === false);
    }

    private function testRejectExpiredToken(): void {
        // Token expirado hace 60 segundos
        $token = JwtService::generateToken(['sub' => 1], -60);

        $val = JwtService::validateToken($token);
        $this->assert('Token con fecha exp en el pasado debe ser RECHAZADO como expirado', $val['valid'] === false && str_contains($val['error'] ?? '', 'expirado'));
    }

    private function testBase64UrlEncodingSafety(): void {
        $sample = "Texto de prueba con caracteres especiales /+== y acentos: café, diseño";
        $encoded = JwtService::base64UrlEncode($sample);
        $decoded = JwtService::base64UrlDecode($encoded);

        $this->assert('Codificación y decodificación Base64Url debe ser idéntica y sin caracteres problemáticos (+, /)', $sample === $decoded && !str_contains($encoded, '+') && !str_contains($encoded, '/'));
    }
}
