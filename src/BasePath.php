<?php

namespace Middlewares;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BasePath implements MiddlewareInterface
{
    /**
     * @var string The path prefix to remove
     */
    private $basePath;

    /**
     * @var bool Whether or not add the base path to the Location header if exists
     */
    private $fixLocation = false;

    /**
     * Configure the base path of the request.
     *
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = rtrim((string) $basePath, '/');

        if (substr($this->basePath, 0, 1) !== '/') {
            $this->basePath = '/'.$this->basePath;
        }
    }

    /**
     * Whether fix the Location header in the response if exists.
     *
     * @param bool $fixLocation
     *
     * @return self
     */
    public function fixLocation($fixLocation = true)
    {
        $this->fixLocation = (bool) $fixLocation;

        return $this;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $uri = $request->getUri();
        $request = $request->withUri($uri->withPath($this->removeBasePath($uri->getPath())));

        $response = $handler->handle($request);

        if ($this->fixLocation && $response->hasHeader('Location')) {
            $location = Utils\Factory::createUri($response->getHeaderLine('Location'));

            if ($location->getHost() === '' || $location->getHost() === $uri->getHost()) {
                $location = $location->withPath($this->addBasePath($location->getPath()));

                return $response->withHeader('Location', (string) $location);
            }
        }

        return $response;
    }

    /**
     * Removes the basepath from a path.
     *
     * @param string $path
     *
     * @return string
     */
    private function removeBasePath($path)
    {
        if (strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath)) ?: '';
        }

        if (substr($path, 0, 1) !== '/') {
            return '/'.$path;
        }

        return $path;
    }

    /**
     * Adds the basepath to a path.
     *
     * @param string $path
     *
     * @return string
     */
    private function addBasePath($path)
    {
        if (strpos($path, $this->basePath) === 0) {
            return $path;
        }

        return str_replace('//', '/', $this->basePath.'/'.$path);
    }
}
