<?php

namespace Tests\Feature\Http\Controllers\Settings;

use Tests\TestCase;
use App\User;

class AccountControllerTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    public function testIndex()
    {
        $response = $this->get(route('settings.account.index'));
        $response->assertOk();
    }

    public function testDestroy()
    {
        $this->actingAs($this->user);
        $this->assertAuthenticatedAs($this->user);
        $response = $this->delete(route('settings.account.destroy', $this->user));
        $response->assertRedirect();
        $this->assertGuest();

        $this->assertNull(User::find($this->user->id));
    }
}
