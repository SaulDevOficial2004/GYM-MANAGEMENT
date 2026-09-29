<?php

use PHPUnit\Framework\TestCase;

abstract class FeatureCase extends TestCase
{
    protected static string $base;
    protected static mysqli $db;

    public static function setUpBeforeClass(): void
    {
        self::$base = getenv('TEST_BASE_URL') ?: 'http://localhost:8000';

        self::$db = new mysqli('localhost', 'root', 'root', 'profitnessgym_test');
        self::$db->set_charset('utf8mb4');

        self::$db->query("INSERT IGNORE INTO roles (id, nombre) VALUES (1, 'Administrador'), (2, 'Recepcionista'), (3, 'Dueño')");

        self::asegurarUsuario('Feature Admin', '9100000001', 1);
        self::asegurarUsuario('Feature Recep', '9100000002', 2);
    }

    private static function asegurarUsuario(string $nombre, string $telefono, int $rolId): void
    {
        $existe = self::$db->query(
            "SELECT id FROM usuarios WHERE telefono = '" . self::$db->real_escape_string($telefono) . "' LIMIT 1"
        );

        $hash = password_hash('Feature123!', PASSWORD_DEFAULT);

        if ($existe && $existe->num_rows > 0) {
            $stmt = self::$db->prepare("UPDATE usuarios SET nombre = ?, password = ?, rol_id = ?, activo = 1 WHERE telefono = ?");
            $stmt->bind_param('ssis', $nombre, $hash, $rolId, $telefono);
        } else {
            $stmt = self::$db->prepare("INSERT INTO usuarios (nombre, telefono, password, rol_id, activo) VALUES (?, ?, ?, ?, 1)");
            $stmt->bind_param('sssi', $nombre, $telefono, $hash, $rolId);
        }

        $stmt->execute();
        $stmt->close();
    }

    protected function nuevaCookie(): string
    {
        return tempnam(sys_get_temp_dir(), 'gmscookie');
    }

    /**
     * @return array{int, string}
     */
    protected function http(string $metodo, string $ruta, $cuerpo = null, array $cabeceras = [], string $jar = null): array
    {
        $ch = curl_init(self::$base . $ruta);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);

        if ($jar !== null) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $jar);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $jar);
        }

        if ($cuerpo !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $cuerpo);
        }

        if ($cabeceras !== []) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $cabeceras);
        }

        $cuerpoResp = curl_exec($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$codigo, (string) $cuerpoResp];
    }

    protected function login(string $telefono, string $jar): void
    {
        [$codigo, $cuerpo] = $this->http(
            'POST',
            '/api/login_api.php',
            json_encode(['telefono' => $telefono, 'password' => 'Feature123!']),
            ['Accept: application/json', 'Content-Type: application/json'],
            $jar
        );

        $this->assertSame(200, $codigo, 'Login de prueba falló: ' . $cuerpo);
    }

    protected function tokenCsrf(string $jar): string
    {
        [$codigo, $cuerpo] = $this->http('GET', '/pagina.php', null, [], $jar);

        $this->assertSame(200, $codigo);

        preg_match('/<meta name="csrf-token" content="([^"]+)"/', $cuerpo, $m);

        $this->assertNotEmpty($m[1] ?? null, 'Sin meta CSRF');

        return $m[1];
    }
}
