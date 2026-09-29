<?php

require_once __DIR__ . '/FeatureCase.php';

class CsrfTest extends FeatureCase
{
    public function test_post_sin_token_devuelve_419(): void
    {
        $jar = $this->nuevaCookie();
        $this->login('9100000001', $jar);

        [$codigo] = $this->http(
            'POST',
            '/api/sell_product.php',
            '{}',
            ['Accept: application/json', 'Content-Type: application/json'],
            $jar
        );

        $this->assertSame(419, $codigo);
    }

    public function test_post_con_token_no_devuelve_419(): void
    {
        $jar = $this->nuevaCookie();
        $this->login('9100000001', $jar);
        $token = $this->tokenCsrf($jar);

        [$codigo] = $this->http(
            'POST',
            '/api/sell_product.php',
            '{}',
            ['Accept: application/json', 'Content-Type: application/json', 'X-CSRF-Token: ' . $token],
            $jar
        );

        $this->assertNotSame(419, $codigo);
    }

    public function test_get_no_requiere_token(): void
    {
        $jar = $this->nuevaCookie();
        $this->login('9100000001', $jar);

        [$codigo] = $this->http('GET', '/api/session_status.php', null, [], $jar);

        $this->assertSame(200, $codigo);
    }
}
