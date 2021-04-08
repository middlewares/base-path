<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\BasePath;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

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
                'project-name/public/',
                '/project-name/public',
                '/',
                '/project-name/public/',
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
            ],
        ];
    }

    /**
     * @dataProvider pathProvider
     */
    public function testBasePath(string $basePath, string $uri, string $result, string $location = null)
    {
        $request = Factory::createServerRequest('GET', $uri);

        $response = Dispatcher::run([
            (new BasePath($basePath))->fixLocation(),

            function ($request) use ($location) {
                echo $request->getUri()->getPath();

                $response = Factory::createResponse();

                if ($location) {
                    return $response->withHeader('Location', (string) $request->getUri());
                }

                return $response;
            },
        ], $request);

        $this->assertEquals($result, (string) $response->getBody());
        $this->assertEquals($location, $response->getHeaderLine('Location'));
    }

    public function attributePathProvider()
    {
        return [
            [
                'project-name/public/',
                'http://example.com/project-name/public',
                '/project-name/public',
            ],
            [
                'project-name/public/',
                '/project-name/public',
                '/project-name/public',
            ],
            [
                '/other/path',
                'http://example.com/project-name/public',
                '/project-name/public',
            ],
            [
                '/project-name',
                'http://example.com/project-name/public',
                '/project-name/public',
            ],
            [
                '',
                'http://example.com/foo',
                '/foo',
            ],
            [
                '',
                'http://example.com',
                '',
            ],
            [
                '/',
                '/hello',
                '/hello',
            ],
        ];
    }

    /**
     * @dataProvider attributePathProvider
     */
    public function testAttribute(string $basePath, string $uri, string $result)
    {
        $request = Factory::createServerRequest('GET', $uri);

        $response = Dispatcher::run([
            (new BasePath($basePath))->attribute('custom-attribute-name'),

            function ($request) {
                echo $request->getAttribute('custom-attribute-name');
            },
        ], $request);

        $this->assertEquals($result, (string) $response->getBody());
    }
}
