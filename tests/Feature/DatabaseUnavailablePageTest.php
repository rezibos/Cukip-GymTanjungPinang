<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Route;
use PDOException;
use Tests\TestCase;

class DatabaseUnavailablePageTest extends TestCase
{
    public function test_database_connection_error_renders_friendly_service_unavailable_page(): void
    {
        Route::get('/database-connection-test', function (): void {
            throw $this->connectionException();
        });

        $response = $this->get('/database-connection-test');

        $response->assertServiceUnavailable()
            ->assertHeader('Retry-After', '30')
            ->assertViewIs('errors.database-unavailable')
            ->assertSeeText('Database tidak terhubung')
            ->assertSeeText('Coba lagi')
            ->assertSeeText('CukipGym.')
            ->assertDontSeeText('SQLSTATE')
            ->assertDontSeeText('127.0.0.1');
    }

    public function test_database_connection_error_returns_a_safe_json_response_for_api_requests(): void
    {
        Route::get('/api/database-connection-test', function (): void {
            throw $this->connectionException();
        });

        $response = $this->getJson('/api/database-connection-test');

        $response->assertServiceUnavailable()
            ->assertHeader('Retry-After', '30')
            ->assertExactJson([
                'message' => 'Layanan data sedang tidak tersedia. Silakan coba lagi beberapa saat lagi.',
                'code' => 'DATABASE_UNAVAILABLE',
            ]);
    }

    public function test_other_query_errors_keep_the_default_error_response(): void
    {
        config(['app.debug' => false]);

        Route::get('/query-error-test', function (): void {
            $previous = new PDOException('SQLSTATE[42000]: Syntax error', 1064);
            $previous->errorInfo = ['42000', 1064, 'Syntax error'];

            throw new QueryException('mysql', 'invalid sql', [], $previous);
        });

        $response = $this->get('/query-error-test');

        $response->assertInternalServerError()
            ->assertDontSeeText('Database tidak terhubung')
            ->assertDontSeeText('Syntax error');
    }

    private function connectionException(): QueryException
    {
        $previous = new PDOException('SQLSTATE[HY000] [2002] Connection refused', 2002);
        $previous->errorInfo = ['HY000', 2002, 'Connection refused'];

        return new QueryException('mysql', 'select * from sessions', [], $previous);
    }
}
