Add agreement widget on Sign up Form
====================================

User registration
-----------------

During registration (sign up), a user is typically required to agree to your application's terms of service. The DocumaticBundle makes it easy to implement this feature.

Ensure that doc on [frontend usage](Resources/doc/frontend.md) is followed, specially "Allow users to agree terms" part. Then,

Add a widget of type SignatureFormType to your signup form. The default `validation_group` for the widget is [`Register`, `Default`]. You are free to change the `validation_group` of the widget as you need.



```php
// ...
use Documatic\Bundle\DocumaticBundle\Form\Type\SignatureFormType;
// ...

class RegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // ...

        $builder->add(
            'agree',
            SignatureFormType::class
        );
    }
}
```
**Note:** The signature widget gets displayed only if you have atleast one agreement with a finalized version.

If you are using FOSUserBundle, that it. The DocumaticBundle creates the signatures for the newly created user. Else you will have to modify your registration/sign up process to include a line of code after you save the user as:

```php
class RegisterController
{
    public function registerAction(Request $request)
    {
        // ...

        $user = new User();
        
        // ...
        if ($form->isValid()) {
            $user->save();
            
            $this->get('documatic.signature.manager')->signAll($user);
        }
        
        // ...
    }
}
```
