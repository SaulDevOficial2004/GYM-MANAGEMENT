<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function test_database_connection_is_working(): void
    {
        $testConnect = $GLOBALS['testConnect'];

        $this->assertInstanceOf(
            mysqli::class,
            $testConnect
        );

        $this->assertSame(
            0,
            $testConnect->connect_errno
        );
    }

    public function test_database_is_profitnessgym_test(): void
    {
        $testConnect = $GLOBALS['testConnect'];

        $result = $testConnect->query(
            'SELECT DATABASE() AS database_name'
        );

        $this->assertNotFalse($result);

        $row = $result->fetch_assoc();

        $this->assertSame(
            'profitnessgym_test',
            $row['database_name']
        );
    }

    public function test_personas_table_is_empty(): void
    {
        $testConnect = $GLOBALS['testConnect'];

        $result = $testConnect->query(
            'SELECT COUNT(*) AS total FROM personas'
        );

        $this->assertNotFalse($result);

        $row = $result->fetch_assoc();

        $this->assertSame(
            0,
            (int)$row['total']
        );
    }
}
