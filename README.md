<a href="https://www.wieni.be">
    <img src="https://www.wieni.be/themes/custom/drupack/logo.svg" alt="Wieni logo" title="Wieni" align="right" height="60" />
</a>

wmsingles
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmsingles/v/stable)](https://packagist.org/packages/wieni/wmsingles)
[![Total Downloads](https://poser.pugx.org/wieni/wmsingles/downloads)](https://packagist.org/packages/wieni/wmsingles)
[![License](https://poser.pugx.org/wieni/wmsingles/license)](https://packagist.org/packages/wieni/wmsingles)

> Singles are used for one-off bundles, e.g. a bundle with exactly ONE entity

## Usage

```php
$service = \Drupal::service('wmsingles');
$homeEntity = $service->getSingleByBundle('home');

```
