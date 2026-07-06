<?php

namespace Tests\Unit;

use Tests\TestCase;

class SessionConfigurationTest extends TestCase
{
    public function test_session_driver_defaults_to_file_when_not_configured(): void
    {
        putenv('SESSION_DRIVER');
        unset($_ENV['SESSION_DRIVER'], $_SERVER['SESSION_DRIVER']);

        $config = require base_path('config/session.php');

        $this->assertSame('file', $config['driver']);
    }
}
