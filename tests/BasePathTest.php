<?php

namespace Middlewares\Tests;

use PHPUnit\Framework\TestCase;
use Middlewares\BasePath;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class BasePathTest extends TestCase
{
    public function pathProvider()
    {
        return [
            [
                'project-name/public/',
                'http://example.com/project-name/public',
                '/',
                'http://example.com/project-name/public/',
            ],
            [
                '/other/path',
                'http://example.com/project-name/public',
                '/project-name/public',
                'http://example.com/other/path/project-name/public',
            ],
            [
                '/project-name',
                'http://example.com/project-name/public',
                '/public',
                'http://example.com/project-name/public',
            ],
            [
                '',
                'http://example.com/foo',
                '/foo',
                'http://example.com/foo',
            ],
            [
                '',
                'http://example.com',
                '/',
                'http://example.com/',
            ],
            [
                '/',
                '/hello',
                '/hello',
                '/hello',
            ],
        ];
    }

    /**
     * @dataProvider pathProvider
     */
    public function testBasePath($basePath, $uri, $result, $location)
    {
        $request = Factory::createServerRequest([], 'GET', $uri);

        $response = Dispatcher::run([
            (new BasePath($basePath))->fixLocation(),

            function ($request) {
                echo $request->getUri()->getPath();

                return Factory::createResponse()
                    ->withHeader('Location', (string) $request->getUri());
            },
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals($result, (string) $response->getBody());
        $this->assertEquals($location, $response->getHeaderLine('Location'));
    }
}