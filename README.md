DocumaticBundle
============


Installation
------------

Add the bundle to your project's `composer.json`:

```json
{
    "require": {
        "uam/tos-bundle": "@stable",
        ...
    }
}
```

Run `composer install` or `composer update` to install the bundle:

``` bash
$ composer update
```


Enable the bundle in the app's kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Documatic\Bundle\DocumaticBundle\DocumaticBundle(),
    );
}
```

If your composer.json does not include the post-install or post-update `installAssets` script handler, then run the following command:

``` bash
$ php app/console assets:install
```

or

``` bash
$ php app/console assets:install --symlink
```
