<?php

namespace Tests\Suites\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Styde\Enlighten\Example;
use Styde\Enlighten\ExampleGroup;
use Tests\App\Models\User;
use Tests\TestCase;

/**
 * @testdox Shows the user's information
 * @description This endpoint allows you to get all the info from a specific user.
 */
class ShowUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox Get user data by ID
     * @description Retrieves the public-user data
     */
    public function shows_user_data(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create([
            'name' => 'Duilio Palacios',
            'email' => 'user@example.test'
        ]);

        $response = $this->get(route('user.show', ['user' => $user]));

        $response
            ->assertOk()
            ->assertViewIs('user.show')
            ->assertSee('Duilio Palacios')
            ->assertSee('user@example.test');

        tap(ExampleGroup::first(), function (ExampleGroup $exampleGroup) {
            $this->assertSame('Tests\Suites\Feature\ShowUserTest', $exampleGroup->class_name);
            $this->assertSame("Shows the user's information", $exampleGroup->title);
            $this->assertSame('This endpoint allows you to get all the info from a specific user', $exampleGroup->description);
        });

        tap(Example::first(), function (Example $example) use ($user) {
            $this->assertSame('Get user data by ID', $example->title);
            $this->assertSame('Retrieves the public-user data', $example->description);
            $this->assertSame('GET', $example->request_method);
            $this->assertSame("user/{$user->id}", $example->request_path);
            $this->assertSame('user/{user}', $example->route);
            $this->assertSame([
                [
                    'name' => 'user',
                    'pattern' => '\d+',
                    'optional' => false,
                ]
            ], $example->route_parameters);

            $this->assertStringContainsString($user->name, $example->response_body);
            $this->assertStringContainsString($user->email, $example->response_body);

            $this->assertStringContainsString('{{ $user->name }}', $example->response_template);
            $this->assertStringContainsString('{{ $user->email }}', $example->response_template);
        });
    }
}
