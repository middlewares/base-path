<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');

        if (substr($this->basePath, 0, 1) !== '/') {
            $this->basePath = '/'.$this->basePath;
        }
    }

    /**
     * Whether fix the Location header in the response if exists.
     */
    public function fixLocation(bool $fixLocation = true): self
    {
        $this->fixLocation = $fixLocation;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
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
     */
    private function removeBasePath(string $path): string
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
     */
    private function addBasePath(string $path): string
    {
        if (strpos($path, $this->basePath) === 0) {
            return $path;
        }

        return str_replace('//', '/', $this->basePath.'/'.$path);
    }
}
