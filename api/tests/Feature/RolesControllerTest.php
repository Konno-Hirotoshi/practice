<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\Base\TestCase;

class RolesControllerTest extends TestCase
{
    /**
     * @dataProvider \Tests\Feature\RolesControllerProvider::test_search
     */
    public function test_search($params, $database, $code, $content)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }
        $response = $this->get('/roles' . $params);
        $response->assertJson($content);
        $response->assertStatus($code);
    }

    /**
     * @dataProvider \Tests\Feature\RolesControllerProvider::test_create
     */
    public function test_create($params, $database, $code, $content, $afterDatabase)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }

        $response = $this->post('/roles/create', $params);
        $response->assertJson($content);
        $response->assertStatus($code);

        foreach ($afterDatabase as $tableName => $rows) {
            foreach ($rows as $row) {
                $this->assertDatabaseHas($tableName, $row);
            }
            $this->assertDatabaseCount($tableName, count($rows));
        }
    }

    /**
     * @dataProvider \Tests\Feature\RolesControllerProvider::test_edit
     */
    public function test_edit($id, $params, $database, $code, $content, $afterDatabase)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }

        $response = $this->post('/roles/edit/' . $id, $params);
        $response->assertJson($content);
        $response->assertStatus($code);

        foreach ($afterDatabase as $tableName => $rows) {
            foreach ($rows as $row) {
                $this->assertDatabaseHas($tableName, $row);
            }
            $this->assertDatabaseCount($tableName, count($rows));
        }
    }

    /**
     * @dataProvider \Tests\Feature\RolesControllerProvider::test_delete
     */
    public function test_delete($id, $database, $code, $content, $afterDatabase)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }

        $response = $this->post('/roles/delete/' . $id);
        $response->assertJson($content);
        $response->assertStatus($code);

        foreach ($afterDatabase as $tableName => $rows) {
            foreach ($rows as $row) {
                $this->assertDatabaseHas($tableName, $row);
            }
            $this->assertDatabaseCount($tableName, count($rows));
        }
    }
}
