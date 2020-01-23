# Internet check

A small component to check if the internet is accessible. Original idea by [Stackoverflow #4860365](http://stackoverflow.com/questions/4860365/determine-in-php-script-if-connected-to-internet).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
 
# Usage

The very basic usage (with default parameters) looks like this:

```php
$available = \lfischer\internet\Internet::available();
```

But of course there are some options you can provide to handle the responses as you need them:

```php
use \lfischer\internet\Internet;
use \lfischer\internet\InternetException;
use \lfischer\internet\InternetProblemException;

// Check the availability by connecting to Google on port 80.
$available = (new Internet('www.google.com', 80))->check();

//
try {
    $internet = new Internet(
        'www.google.com', 
        80, 
        Internet::EXCEPTION_ON_UNAVAILABILITY + Internet::PROBLEM_AS_EXCEPTION
    );

    $available = $internet->check();
} catch (InternetException $e) {
    // The internet is not available.
    $internet->getErrorString();
    $internet->getErrorNumber();
    $e->getMessage();
} catch (InternetProblemException $e) {
    // There was a problem while checking the availability.
     $internet->getErrorString();
     $internet->getErrorNumber();
     $e->getMessage();
 }
```

## Options

You can pass some options to change the behaviour in case of problems.

- `Internet::EXCEPTION_ON_UNAVAILABILITY`\
Will throw an `InternetException` exception if the internet is unavailable (instead of returning `false`).
- `Internet::PROBLEM_AS_EXCEPTION`\
Will throw an `InternetProblemException` exception if the internet availability could not be checked due to a problem (instead of returning `false`).
- `Internet::PROBLEM_AS_TRUE`\
Will return `true` in case of a problem during the availability check since you'd like to assume something else went wrong.

# Static code analysis and code style

The code is being statically analyzed with the help of [vimeo/psalm](https://packagist.org/packages/vimeo/psalm). The PSR2 code style will be checked/applied with the help of [friendsofphp/php-cs-fixer](https://packagist.org/packages/friendsofphp/php-cs-fixer).
