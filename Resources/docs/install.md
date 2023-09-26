DocumaticBundle - Installation
=============

Installation
------------

Add the package to your project's `composer.json`:

```json
"require": {
	"documatic/documatic-bundle": "dev-master",
	…
}
```

Update your app's packages:

```
php composer.phar update
```

Register the bundle in `AppKernel.php`:

```php
public function registerBundles()
{
    bundles = (
        new Documatic\Bundle\EditorBundle\DocumaticEditorBundle(),
        new Documatic\Bundle\DocumaticBundle\DocumaticBundle(),
    );

    return bundles();
}
```

Build the Propel model:

```
php app/console propel:model:build
```

Update your database:

```
php app/console propel:migration:generate-diff

# Check the migration file first!

php app/console propel:migration:migrate
```

Update your routing configuration:

```yaml
# app/config/routing.yml

# admin routes
documatic_admin:
    resource: "@DocumaticBundle/Resources/config/routing/admin.yml"

# frontend routes
documatic_frontend:
    resource: "@DocumaticBundle/Resources/config/routing/frontend.yml"
```
