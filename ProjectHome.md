## What ##
Framework agnostic, easy to integrate or extend, generic client file serving tool, PHP Client Booster contains different files able to merge, minify, pre-compress, and serve whatever kind of client file forcing browser cache when possible and avoiding server stress.
  * JavaScript or CSS via [YUI Compressor](http://developer.yahoo.com/yui/compressor/)
  * @font-face compatible fonts: WOFF, TTF, EOT, others
  * static XML, JSON, HTML, plain text
  * every other file that could be boosted via gzip/deflate compression

## Why ##
Run-time compression could be a massive waste of resources. While it could be worth it for dynamic responses, such a generic html page, ad hoc XML or JSON responses, it could improve, rather than reduce, both server side stess and response time.
Specially in these days where developer are including, as example, big client files as fonts used for HTML5 @font-face, massive libraries or related CSS, pre-compression could save server stress improving overall application performances.

## Faster Than GzHandler! ##
This table represents benchmark avoiding browser cache. Each response has been performed 10 times and the server generated each time a full status 200 response with the jQuery library.
| **PHP Client Booster** | **Plain Text** | **ob\_start('ob\_gzhandler')** |
|:-----------------------|:---------------|:-------------------------------|
| 81.50 ms               | 88.80 ms       | 191.20 ms                      |

This table represents benchmark using browser cache. Each response has been performed 10 times and the server generated each time a 304 response (except ob\_gzhandler) with the jQuery library.
| **PHP Client Booster** | **Plain Text** | **ob\_start('ob\_gzhandler')** |
|:-----------------------|:---------------|:-------------------------------|
| 72.10 ms               | 67.00 ms       | 187.30 ms                      |

**ob\_gzhandler does not use browser cache**, which is basically the reason there is no difference between normal and cached test.

It is possible to test directly results via the bench folder provided with PHP Client Booster.

## Where ##
Every host with support for PHP 4, PHP 5, or PHP 5.3 and 6 included plus every development environment able to integrate a couple of files in one or more projects.

## How ##
Well, I need some time to write wiki pages so please be patience. Right now you can find everything you need in the zip. Unpack in your htdocs, try to understand folders hierarchy, in any case mutable to let us put builders outside the public domain, and read comments ... there are really few files to understand but you could start with **build/jQuery.php** and **client/index.php**

## A Bit Of History ##
For those who remember my packed.it and MyMin project, this is a PHP evolution almost as twice as fast and better than precedent used strategies used in my good old project. That was based on a proprietary minifier created for almost every language but it could not compete with YUI Compressor ratio/features this is why this time I've focused code using only strictly related functions in a totally cross-platform solution.