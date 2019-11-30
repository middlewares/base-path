# middlewares/base-path

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to remove the prefix from the uri path of the request. This is useful to combine with routers if the root of the website is in a subdirectory. For example, if the root of your website is `/web/public`, a request with the uri `/web/public/post/34` will be converted to `/post/34`.

## Requirements

* PHP >= 7.2
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http message implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/base-path](https://packagist.org/packages/middlewares/base-path).

```sh
composer require middlewares/base-path
```

## Usage

Set the prefix to remove:

```php
Dispatcher::run([
	new Middlewares\BasePath('/base/path')
]);
```

### fixLocation

Used to add the prefix to the `Location` header (for redirects). For example:

```php
$response = Dispatcher::run([
    (new Middlewares\BasePath('/base/path'))->fixLocation(),

    function () {
        return Factory::createResponse(301)->withHeader('Location', '/post/1');
    }
]);

echo $response->getHeader('Location'); // Returns /base/path/post/1
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/base-path.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/base-path/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/base-path.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/base-path.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/base-path
[link-travis]: https://travis-ci.org/middlewares/base-path
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/base-path
[link-downloads]: https://packagist.org/packages/middlewares/base-path
