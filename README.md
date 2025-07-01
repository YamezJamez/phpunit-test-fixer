# phpunit-test-fixer

A custom [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) rule that automatically prepends `test` to public methods in PHPUnit test classes that don't already start with `test`.

## Before / After

```php
// Before:
public function itDoesSomething() {}

// After:
public function testItDoesSomething() {}
```

## Why?
If like me you keep forgetting to prefix `test` to you test methods, this will help prevent thinking your tests just happened to pass first time!

### TO DO
- Create a fixer to add the `#[Test]` attribute to classes (And replace where the `test` prefix is used)
