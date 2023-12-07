<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\Base\TestCase;

class SessionsControllerTest extends TestCase
{

    /**
     * @dataProvider \Tests\Feature\SessionsControllerProvider::test_login
     */
    public function test_login($param, $database, $code, $content)
    {
        foreach($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }
        $response = $this->post('/login', $param);
        $response->assertJson($content);
        $response->assertStatus($code);
    }

    /**
     * @dataProvider \Tests\Feature\SessionsControllerProvider::test_logout
     */
    public function test_logout($key, $code, $content)
    {
        $response = ($key !== null)
            ? $this->withCookie('key', $key)->post('/logout')
            : $this->post('/logout');
        $response->assertJson($content);
        $response->assertStatus($code);
    }
}
