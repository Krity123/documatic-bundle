Frontend usage
==============

The bundle provides several elements to help you make use of your terms of service in your frontend pages (that is, the pages that are visible to your users).

Routing
-------

```yaml
# app/config/routing.yml

cp_terms_bundle:
    resource: "@DocumaticBundle/Resources/config/routing/frontend.yml"
```
Add the `frontend.yml` routing configuration to your application.

By default, the routing configuration sets the prefix `/tos` for all frontend pages. You are free to change this by defining your own routing configuration instead of using the one supplied by default.

The CPTermsBundle provides you with the following 3 routes:

* `documatic_frontend_agreement` (default url: `/tos`): displays all the agreements, and allow users to agree to the latest finalized versions of all the agreement if he/she has already not done so.
* `documatic_frontend_agreement_show` (default url: `/tos/{stub}`): displays latest version for an selected agreement.
* `documatic_frontend_agreement_diff` (default url: `/tos/{stub}/diff`): displays and highlights the differences between the latest version and the previous signed version for an selected agreement by the user.

Displaying the terms of service
------------------------------

The route named `documatic_frontend_agreement` (default url: `/tos`) points to the page that displays list of the agreements with a link to display the latest version of the agreement.

The route named `documatic_frontend_agreement_show` (default url: `/tos/{stub}`) At any time, will only display the latest finalized version for a given agreement.

A version finalized at an earlier date are never displayed.

Non-finalized versions are never displayed.

Allowing users to agree to terms
--------------------------------

As seen in [General concepts](concepts.md), in general the user's agreement to your terms is best collected at the time a new user opens an account (during the process called either registration or signup). This is also possible with the DocumaticBundle (see [Signup](Resources/doc/signup.md)).

However there are several scenarios where you will need to obtain the agreement of a user who is already registered, such as:

* you have published a revised version of your terms of service, and need to obtain the renewed agreement of the user
* you previously had no terms of service and are intrudocing them to an app that already has registered users
* for some reason, you have not recorded the agreement of some registered users 

The route named `documatic_frontend_agreement` (default url: `/tos`) points to a page where the user can agree to your latest terms.

The page is available anonymously, to view the agreements, but only those users instance implementing `Symfony\Component\Security\Core\User\UserInterface` are allowed to agree to the agreements.

Displaying the differences with earlier terms
---------------------------------------------

In cases where you are seeking the renewed agreement, to your revised terms of service, of a user who has already agreed to a previous version of your terms, it is customary (and understandably justified) to offer your user the possibility to examine the changes in the revised terms, in other words the differences between the previous version that he has already agreed to, and the current, revised version.

The route named `documatic_frontend_agreement_diff` (default url: `/tos/{stub}/diff`) provides such a page. The page will always display the differences between:

* the current version of selected agreement (i.e. the latest finalized version)
* the version of selected agreement that the user has previously agreed to

The page is only available, if user instance of `Symfony\Component\Security\Core\User\UserInterface`
