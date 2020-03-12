wmsingles
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmsingles/v/stable)](https://packagist.org/packages/wieni/wmsingles)
[![Total Downloads](https://poser.pugx.org/wieni/wmsingles/downloads)](https://packagist.org/packages/wieni/wmsingles)
[![License](https://poser.pugx.org/wieni/wmsingles/license)](https://packagist.org/packages/wieni/wmsingles)

> Singles are node types used for one-off pages that have unique content requirements

## Why?
- Singles are node types used for **one-off pages that have unique content requirements**, such as the homepage, an _About Us_ page, a _Contact Us_ page, etc.
- Unlike other node types, singles have **only one node** associated with them
- This concept was taken from [Craft CMS](https://docs.craftcms.com/v2/sections-and-entries.html#singles)

## Installation

This package requires PHP 7.1 and Drupal 8 or higher. It can be
installed using Composer:

```bash
 composer require wieni/wmsingles
```

## How does it work?
When adding a new node type, check the _This is a content type with a single entity._
 checkbox under the _Singles_ tab to mark this node type as a single.

A node of this type will automatically be created and you will not be able 
to create others.

Only users with the `administer wmsingles` permission will be able to delete singles.

An overview with all singles is available at `/admin/content/singles` for 
all users with the `access wmsingles overview` permission. 

## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE) file
for more information.
