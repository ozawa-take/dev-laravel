<?php

namespace Tests\Feature;

use Tests\TestCase;

class RootPageTest extends TestCase
{
    /**
     * @test
     */
    public function test_root_get_ok()
    {
        $response = $this->get('/');
        $response->assertOk();
    }

    /**
     * @test
     */
    public function test_root_get_ok_login_button()
    {
        $response = $this->get('/admin/login');
        // 遷移先が正しいか確認
        $response->assertSee('/admin/login');
    }
}
