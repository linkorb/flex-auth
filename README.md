# flexauth
FlexAuth: independent library for symfony security

Allows switching the `UserProvider` at Runtime using environment variables. 

Dynamic UserProvider that supports multiple backends based on environment variables.

To override which should provider configuration as array for runtime
```FlexAuth\FlexAuthTypeProviderInterface```

Using ```FlexAuth\FlexAuthTypeProviderFactory::fromEnv('FLEX_AUTH'')``` and define env variables in format
`type?param1=value1&param2=value2&param3=value3`

Example define environment variable

```
## Use memory provider
FLEX_AUTH=memory?users=alice:4l1c3:ROLE_ADMIN;ROLE_EXAMPLE,bob:b0b:ROLE_EXAMPLE)
## Or use userbase provider
FLEX_AUTH=userbase?dsn=https://username:password@userbase.example.com
## Or use the entity provider
FLEX_AUTH=entity?class=\App\Entities\User&property=username
## Or use a JWT provider
FLEX_AUTH=jwt?algo=RS256&publickey=@\cert\public_key.key&private_key=@\cert\privite_key.key&userField=username&groupField=permissions
```

A long form format could be supported like this:
```
FLEX_AUTH=entity
FLEX_AUTH_ENTITY_CLASS=\App\Entities\User
FLEX_AUTH_ENTITY_PROPERTY=username
```

Dynamically flex type provider example.
```php
class MyFlexAuthTypeProvider implements FlexAuthTypeProviderInterface {
    protected $className = \App\Entities\User::class; // can be change in runtime
    protected $propery = 'id';
    
    //...
    public function provide(): array { // will be call every time
        return [
            'type' => 'entity',
            'class' => $this->className,
            'propery' => $this->propery, // dynamic user identificator
        ];
    }
    
    public function switchToEmail() {
        $this->propery = 'email';
    }
}
```

Full working example you can see ```/test/AuthenticationTest::testAuthenticate```

Run tests
```
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/
```

### Links

[FlexAuthBundle - symfony bundle](https://github.com/linkorb/flex-auth-bundle).
[Demo](https://github.com/linkorb/flex-auth-bundle-demo)

[FlexAuthProvider - silex provider](https://github.com/linkorb/flex-auth-provider).
[Demo](https://github.com/linkorb/flex-auth-provider-demo)

[The Security Component(Symfony Docs)](https://symfony.com/doc/current/components/security.html)