<?php

namespace Middlewares\Tests;

use Middlewares\BasePath;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use mindplay\middleman\Dispatcher;

class BasePathTest extends \PHPUnit_Framework_TestCase
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
        $request = new ServerRequest([], [], $uri);

        $response = (new Dispatcher([
            (new BasePath($basePath))->fixLocation(),

            function ($request) {
                $response = new Response();
                $response->getBody()->write((string) $request->getUri()->getPath());

                return $response->withHeader('Location', (string) $request->getUri());
            },
        ]))->dispatch($request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals($result, (string) $response->getBody());
        $this->assertEquals($location, $response->getHeaderLine('Location'));
    }
}
