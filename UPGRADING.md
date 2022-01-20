# Upgrade Guide

This document describes breaking changes and how to upgrade. For a
complete list of changes including minor and patch releases, please
refer to the [`CHANGELOG`](CHANGELOG.md).

## 3.0.0
A lot of classes, config, tokens, routes, etc. have changed.

1. Install the new `node_singles` module

```bash
composer install drupal/node_singles
drush en node_singles
```

2. Use the bash script in `scripts/update-to-v3.sh` for an 
automatic upgrade of your project. Paths that have to be scanned should be passed as arguments:

```bash
chmod +x ./public/modules/contrib/wmsingles/scripts/update-to-v3.sh
./public/modules/contrib/wmsingles/scripts/update-to-v3.sh config/* public/modules/custom/* public/themes/custom/* public/sites/*
```

3. Apply any changes:

```bash
drush cr
drush cim -y
```

4. Deploy these changes to all your environments
5. Remove the old module files:

```bash
composer remove wieni/wmsingles
```
