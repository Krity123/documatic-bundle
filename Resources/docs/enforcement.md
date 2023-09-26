Enforcement of the agreements
=============================

Enforcing agreement means making sure that user of the site has agreed to latest terms of your site. Everytime you publish a new version of your terms of service, If the terms have changed since last agreement between users and site, then again latest terms must be agreed.

By default users are not allowed to visit any page excepts for urls mentioned in [frontend doc](Resources/doc/frontend.md). Logged in Users are redirected to route `documatic_frontend_agreement`(default url: `\tos`) if they have not agreed to the latest version of all of the agreement.

If you want your users to access others pages beside pages mentioned in [frontend doc](Resources/doc/frontend.md), even if they have not agreed to all the latest terms. You can override `Documatic\Bundle\DocumaticBundle\EventListener\SignatureListener::getIgnoredControllers` method to include the controller you want to be Ignored.

Step 1: Create `SignatureListener` class:

```php
<?php

namespace <YourNamespace>;

use Documatic\Bundle\DocumaticBundle\EventListener\SignatureListener as BaseSignatureListener;

class SignatureListener extends BaseSignatureListener
{
    protected function getIgnoredControllers()
    {
        return array_merge(
            parent::getIgnoredControllers(),
            array(
                // your controllers ...
            );
        )
    }
}

```

Step 2: Change the SignatureListener's service class:

```yaml
# .../app/config/services.yml
parameters:
    documatic.signature.listener.class: "<YourNamespace>\\SignatureListener"
```
