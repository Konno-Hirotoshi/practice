<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\Base\TestCase;

class RolesControllerTest extends TestCase
{
    /**
     * @dataProvider \Tests\Feature\RolesControllerProvider::test_search
     */
    public function test_search($params, $database, $responseCode, $responseContent)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }
        $response = $this->get('/roles' . $params);
        $response->assertJson($responseContent);
        $response->assertStatus($responseCode);
    }

    /**
     * @dataProvider \Tests\Feature\RolesControllerProvider::test_create
     */
    public function test_create($params, $database, $responseCode, $responseContent, $afterDatabase)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }

        $response = $this->post('/roles/create', $params);
        $response->assertJson($responseContent);
        $response->assertStatus($responseCode);

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
    public function test_edit($id, $params, $database, $responseCode, $responseContent, $afterDatabase)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }

        $response = $this->post('/roles/edit/' . $id, $params);
        $response->assertJson($responseContent);
        $response->assertStatus($responseCode);

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
    public function test_delete($id, $database, $responseCode, $responseContent, $afterDatabase)
    {
        foreach ($database as $tableName => $rows) {
            DB::table($tableName)->insert($rows);
        }

        $response = $this->post('/roles/delete/' . $id);
        $response->assertJson($responseContent);
        $response->assertStatus($responseCode);

        foreach ($afterDatabase as $tableName => $rows) {
            foreach ($rows as $row) {
                $this->assertDatabaseHas($tableName, $row);
            }
            $this->assertDatabaseCount($tableName, count($rows));
        }
    }
}
