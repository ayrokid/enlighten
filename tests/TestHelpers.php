<?php

namespace Tests;

use Styde\Enlighten\Models\Example;
use Styde\Enlighten\Models\ExampleGroup;
use Styde\Enlighten\Models\Run;

trait TestHelpers
{
    protected function setConfig(array $config)
    {
        $this->app->config->set($config);
    }

    public function createRun(array $attributes = []): Run
    {
        return Run::create(array_merge([
            'branch' => 'main',
            'head' => 'abcde',
            'modified' => false,
        ], $attributes));
    }

    protected function createExample(?ExampleGroup $group = null, string $methodName = 'test_method', string $testStatus = 'passed', string $title = 'Something does something'): Example
    {
        if (is_null($group)) {
            $group = $this->createExampleGroup();
        }

        return Example::create([
            'group_id' => $group->id,
            'method_name' => $methodName,
            'test_status' => $testStatus,
            'title' => $title,
        ]);
    }

    protected function createExampleTest(array $attributes = []) : Example
    {
        return Example::create(array_merge([
            'method_name' => 'something_does_something',
            'title' => 'Something Does something',
            'test_status' => 'passed'
        ], $attributes));
    }

    protected function createExampleGroup(?Run $run = null, $className = null, $title = null, $description = null): ExampleGroup
    {
        if (is_null($run)) {
            $run = $this->createRun();
        }

        return ExampleGroup::create($this->getExampleGroupAttributes([
            'run_id' => $run->id,
            'class_name' => $className,
            'title' => $title,
            'description' => $description,
        ]));
    }

    protected function getExampleGroupAttributes(array $customAttributes = [])
    {
        return array_merge([
            'class_name' => 'Tests\Feature\CreateUserTest',
            'title' => 'Create User',
            'description' => 'User module API'
        ], array_filter($customAttributes));
    }

    protected function createExampleInGroup(ExampleGroup $group): Example
    {
        return Example::create([
            'title' => 'Creates a new user',
            'group_id' => $group->id,
            'method_name' => 'creates_a_new_user',
            'description' => 'register new users in the system.',
        ]);
    }

    protected function createHttpData(Example $example, array $customAttributes = [])
    {
        $example->http_data()->create($this->getHttpDataAttributes($customAttributes));

        return $example->http_data;
    }

    protected function getHttpDataAttributes(array $customAttributes = [])
    {
        return array_merge([
            'request_path' => 'user',
            'request_headers' => [
                "accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "accept-charset" => "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
                "accept-language" => "en-us,en;q=0.5"
            ],
            'request_method' => 'POST',
            'request_query_parameters' => [],
            'request_input' => [
                "name" => "Jeff",
                "email" => "jeff@example.test",
                "password" => "my-password"
            ],
            'route' => 'user',
            'route_parameters' => [],
            'response_status' => 302,
            'response_headers' => [
                "date" => [
                    "Wed, 23 Sep 2020 09:53:15 GMT"
                ],
                "location" => [
                    "http://localhost"
                ],
                "content-type" => [
                    "text/html; charset=UTF-8"
                ],
                "cache-control" => [
                    "no-cache, private"
                ]
            ],
            'response_body' => $this->redirectResponseBody(),
            'response_template' => null
        ], $customAttributes);
    }

    protected function redirectResponseBody()
    {
        return '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8" />
                <meta http-equiv="refresh" content="0;url=\'http://localhost\'" />

                <title>Redirecting to http://localhost</title>
            </head>
            <body>
            Redirecting to <a href="http://localhost">http://localhost</a>.
            </body>
        </html>';
    }
}
