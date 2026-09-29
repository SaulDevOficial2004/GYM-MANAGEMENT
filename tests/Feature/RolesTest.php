<?php

require_once __DIR__ . '/FeatureCase.php';

use PHPUnit\Framework\Attributes\DataProvider;

class RolesTest extends FeatureCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function proveedorSoloAdmin(): array
    {
        $casos = [];

        foreach ([
            'create_receptionist.php',
            'reset_receptionist_password.php',
            'toggle_receptionist.php',
            'update_receptionist.php',
            'update_transferencias.php',
        ] as $archivo) {
            $casos[$archivo . ' POST'] = [$archivo, 'POST'];
        }

        foreach ([
            'get_audit_log.php',
            'get_monthly_sales_current_year.php',
            'get_payment_history.php',
            'get_sales_by_user.php',
            'get_sales_distribution.php',
            'get_sales_history.php',
            'get_sales_last_7_days.php',
        ] as $archivo) {
            $casos[$archivo . ' GET'] = [$archivo, 'GET'];
        }

        return $casos;
    }

    #[DataProvider('proveedorSoloAdmin')]
    public function test_recep_recibe_403_en_admin(string $archivo, string $metodo): void
    {
        $jar = $this->nuevaCookie();
        $this->login('9100000002', $jar);
        $token = $this->tokenCsrf($jar);

        $cuerpo = $metodo === 'POST' ? '{}' : null;
        $cabeceras = $metodo === 'POST'
            ? ['Accept: application/json', 'Content-Type: application/json', 'X-CSRF-Token: ' . $token]
            : [];

        [$codigo] = $this->http($metodo, '/api/' . $archivo, $cuerpo, $cabeceras, $jar);

        $this->assertSame(403, $codigo, $archivo);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function proveedorProtegidos(): array
    {
        $casos = [];

        $post = [
            'confirmar_comprobante.php', 'create_membership.php', 'create_person.php',
            'create_product.php', 'create_visit.php', 'create_visitante.php',
            'delete_coach.php', 'delete_membership.php', 'delete_person.php',
            'delete_product.php', 'disable_member.php', 'enable_person.php',
            'rechazar_comprobante.php', 'rent_towel.php', 'sell_product.php',
            'toggle_membership.php', 'toggle_product.php', 'update_membership.php',
            'update_membership_admin.php', 'update_person.php', 'update_product.php',
            'generar_enlace_cliente.php', 'create_receptionist.php',
            'reset_receptionist_password.php', 'toggle_receptionist.php',
            'update_receptionist.php', 'update_transferencias.php',
        ];

        foreach ($post as $archivo) {
            $casos[$archivo . ' POST'] = [$archivo, 'POST'];
        }

        $get = [
            'session_status.php', 'search_products.php', 'search_visitantes.php',
            'get_audit_log.php', 'get_monthly_sales_current_year.php',
            'get_payment_history.php', 'get_sales_by_user.php',
            'get_sales_distribution.php', 'get_sales_history.php',
            'get_sales_last_7_days.php', 'ver_archivo.php',
        ];

        foreach ($get as $archivo) {
            $casos[$archivo . ' GET'] = [$archivo, 'GET'];
        }

        return $casos;
    }

    #[DataProvider('proveedorProtegidos')]
    public function test_sin_sesion_devuelve_401(string $archivo, string $metodo): void
    {
        $ruta = '/api/' . $archivo;

        if ($metodo === 'GET' && str_starts_with($archivo, 'search_')) {
            $ruta .= '?search=x';
        }

        if ($archivo === 'ver_archivo.php') {
            $ruta .= '?id=1';
        }

        $cuerpo = $metodo === 'POST' ? '{}' : null;
        $cabeceras = $metodo === 'POST' ? ['Accept: application/json', 'Content-Type: application/json'] : [];

        [$codigo] = $this->http($metodo, $ruta);

        $this->assertSame(401, $codigo, $archivo);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function proveedorRecepPermitidos(): array
    {
        $casos = [];

        foreach ([
            'confirmar_comprobante.php', 'create_membership.php', 'create_person.php',
            'create_product.php', 'create_visit.php', 'create_visitante.php',
            'delete_coach.php', 'delete_membership.php', 'delete_person.php',
            'delete_product.php', 'disable_member.php', 'enable_person.php',
            'rechazar_comprobante.php', 'rent_towel.php', 'sell_product.php',
            'toggle_membership.php', 'toggle_product.php', 'update_membership.php',
            'update_membership_admin.php', 'update_person.php', 'update_product.php',
            'generar_enlace_cliente.php',
        ] as $archivo) {
            $casos[$archivo . ' POST'] = [$archivo, 'POST'];
        }

        foreach (['session_status.php', 'ver_archivo.php?id=1'] as $ruta) {
            $casos[$ruta . ' GET'] = [$ruta, 'GET'];
        }

        return $casos;
    }

    #[DataProvider('proveedorRecepPermitidos')]
    public function test_recep_pasa_guardas_en_permitidos(string $ruta, string $metodo): void
    {
        $jar = $this->nuevaCookie();
        $this->login('9100000002', $jar);
        $token = $this->tokenCsrf($jar);

        $cuerpo = $metodo === 'POST' ? '{}' : null;
        $cabeceras = $metodo === 'POST'
            ? ['Accept: application/json', 'Content-Type: application/json', 'X-CSRF-Token: ' . $token]
            : [];

        [$codigo] = $this->http($metodo, '/api/' . $ruta, $cuerpo, $cabeceras, $jar);

        $this->assertNotContains($codigo, [401, 403, 419], $ruta . ' http=' . $codigo);
    }
}
