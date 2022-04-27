# url-tracker
[![build](https://github.com/peter279k/url-tracker/actions/workflows/build.yml/badge.svg)](https://github.com/peter279k/url-tracker/actions/workflows/build.yml)

# Introduction

- It's for developers to track URL programmatically and avoid faking phishing URL with shorten URL.

# Usage

- Using this library is simple. Firstly, installing the library with following command:

```
composer require lee/url-tracker
```

- Then using the following code to track URL easily:


```php
require_once __DIR__ . '/vendor/autoload.php';

use Lee\Tracker;

$url = 'https://bit.ly/grpc-intro';
$trackedResult = Tracker::trackFromUrl($url); // ['https://bit.ly/grpc-intro', 'https://www.slideshare.net/williamyeh/grpc-238408172/williamyeh/grpc-238408172',]
```
