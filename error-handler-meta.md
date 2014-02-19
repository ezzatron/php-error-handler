# Error handler meta document

## 1. Goals

- To raise awareness of the existing climate of incompatible error handling
  strategies.
- To help protect code consumers from unknowingly mixing incompatible libraries,
  packages, frameworks, components, etc.
- To help code producers specify their error handling requirements.
- To promote improved interoperability by encouraging the adoption of
  exception-based error handling as the standard for new projects.

## 2. Why bother?

### 2.1. A brief history of error handling in PHP

#### 2.1.1. Before exceptions

Before the introduction of exceptions, error conditions in PHP were handled
through *error messages*. These error messages were differentiated by
*severity*; most notably errors, warnings, and notices (and eventually
deprecation messages). The severity of errors was used to determine whether
script execution should continue after the error was handled, or if execution
should halt for a serious error.

In addition to their primary role, error messages were typically logged. This
often lead to the error system being harnessed as a simple logging system.

#### 2.1.2. The introduction of exceptions

Shortly after PHP 5 came about, exceptions were introduced as a first-class
feature. Exceptions provided much greater control over how error conditions were
handled. In short, they allowed the developer to anticipate potential problems,
and handle them gracefully. Something which was much more challenging prior to
their introduction.

Despite exceptions being available, PHP retained its existing error message
system alongside the exception system. A special exception designed to represent
an error message was introduced, however. The [ErrorException] class was
specifically designed to contain the information that would normally be
expressed in an error message.

#### 2.1.3. Adoption of exception-throwing error handlers

As PHP 5 matured, developers began to explore using error handlers that used
thrown [ErrorException] instances to replace traditional error handling
strategies. This approach proved successful and popular over time, and is the
typical approach found in today's major frameworks.

### 2.2. The current state of error handling in PHP

PHP is in a state of limbo with regards to error handling. In general, code is
either written to expect a runtime environment where exceptions are thrown to
represent errors, or to expect traditional error handling. Code that is designed
to work identically in either environment is exceedingly rare. This leads to a
hidden, and potentially dangerous, interoperability issue.

#### 2.2.1. Expecting traditional errors

To illustrate the point further, let's use an example scenario. The following
code would be suitable for an environment where *traditional* error handling is
in use:

```php
$path = '/path/to/important/file';
$stream = fopen($path, 'rb');
if (!$stream) {
    mail('jbond@mi5.gov.uk', 'Important file missing', 'Commence operation.');
    throw new FileReadException($path);
}
everythingIsOkay();
```

In case of a problem opening the file, `fopen()` will raise a PHP warning, but
execution will continue, and `fopen()` will return `false`. The code will
correctly identify the error condition, send an important warning email, and
throw a `FileReadException` as expected. Importantly, the `everythingIsOkay()`
function will not be called.

However, what happens in the same situation in an environment where *error
exceptions* are thrown instead? The call to `fopen()` would result in an
[ErrorException] being thrown. Without an appropriate `catch` statement, the
important warning email will never be sent. Additionally, the [ErrorException]
will not be caught by any code expecting a `FileReadException`, which will most
likely result in execution being halted. At least `everythingIsOkay()` is not
called.

#### 2.2.2. Expecting error exceptions

The same example designed for an environment where *error exceptions* are thrown
would look something like:

```php
$path = '/path/to/important/file';
try {
    $stream = fopen($path, 'rb');
} catch (ErrorException $e) {
    mail('jbond@mi5.gov.uk', 'Important file missing', 'Commence operation.');
    throw new FileReadException($path, $e);
}
everythingIsOkay();
```

In case of a problem opening the file, `fopen()` would result in an
[ErrorException] being thrown. The code will correctly identify the error
condition by catching the exception, send an important warning email, and throw
a `FileReadException` as expected (with the [ErrorException] as the 'previous'
exception). Importantly, the `everythingIsOkay()` function will not be called.

However, what happens in the same situation in an environment where
*traditional* error handling is in use? The call to `fopen()` will raise a PHP
warning, but execution will continue. No [ErrorException] is thrown, so the
`catch` statement will have no effect. No warning email will be sent, no
`FileReadException` is thrown, and worst of all, `everythingIsOkay()` will be
called. Everything is definitely *not* okay.

#### 2.2.3. Expecting both exceptions and errors

Let's take a look at the code necessary to handle the same example when
accounting for both error handling approaches as possibilities:

```php
$path = '/path/to/important/file';
$e = null;
try {
    $stream = fopen($path, 'rb');
} catch (ErrorException $e) {
    $stream = false;
}
if (!$stream) {
    mail('jbond@mi5.gov.uk', 'Important file missing', 'Commence operation.');
    throw new FileReadException($path, $e);
}
everythingIsOkay();
```

From this example, it's immediately obvious that accounting for both situations
is extremely tedious. The code is also harder to understand, and will require
increased effort to unit test all possible code branches.

### 2.3. Not having a standard is hurting everyone

Without a formal specification for error handling, few PHP projects are designed
to be truly interoperable in the way they deal with errors, and projects rarely
specify the type of error handling they expect from their runtime environment.
This leads to the situation where consumers of code can unknowingly rely on code
that is fundamentally incompatible with the environment in which it will be run.

Code producers also suffer. The added burden of designing code to work with
multiple possible error handlers is tedious. Incompatible error handling
strategies hinder code re-use, and foster mistrust in the code of others.

## 3. What can be done to address the issue?

### 3.1. Requirements for an effective solution

- Packages / projects / frameworks / components need a way to specify the types
  of error handling they support.
- Consumers who pull in these packages as dependencies need a way to insure that
  they have chosen compatible packages.
- Error handlers form a part of the PHP runtime environment. Ideally, a
  spec-conformant error handler should be installed before any other code is
  executed, even before any class loaders are registered.

This document will suggest two possible options for addressing these
requirements. Both revolve around the dependency manager [Composer], although
similar solutions could easily be extrapolated for other package/dependency
management applications.

### 3.2. Solution A: Error handling management as a first-class Composer feature

This solution involves implementing error handling management by introducing new
Composer features. It's obviously not this document's place to prescribe
features to the Composer project. This is simply intended as an example of an
ideal situation.

A new optional property `error-handling` would be added to the Composer package
configuration schema. This property would allow one of three string values:
`PSR-N`, `traditional`, or `any`, with `any` being the default.

- A value of `PSR-N` would indicate that the package expects a `PSR-N`
  conformant error handler to be installed, where `PSR-N` is the PSR number of
  the error handler specification associated with this meta document.
- A value of `traditional` would indicate that the package expects the error
  handler to behave in the same manner as the built-in PHP handler.
- A value of `any` would indicate that the package is capable of functioning
  under either error handling strategy.

Another optional property `use-error-handling` would be added under the
project-only section ([config]) of the Composer package configuration schema.
This property, used only in root packages, would allow one of two string values:
`PSR-N`, or `traditional`, with `PSR-N` being the default.

- A value of `PSR-N` would indicate that Composer should install a `PSR-N`
  conformant error handler before setting up the class loader.
- A value of `traditional` would indicate that Composer should not install an
  error handler.

During the normal process of dependency resolution, components that require
incompatible error handling strategies would be highlighted as conflicts. This
brings the problem to the attention of the package developer, and allows them to
make informed decisions about how to address the conflict.

In addition to these new properties, some mechanism may have to be introduced to
allow package developers to ignore conflicts.

#### 3.2.1. Pros

- Error handling requirements are expressed clearly and succinctly.
- The error handler is guaranteed to be installed before any errors occur.
- First-class support in Composer would allow for clearer conflict messages.

#### 3.2.2. Cons

- Introducing new Composer features would take time and effort.

#### 3.2.3. Example package configurations

Package requiring PSR-N error handling:

```json
{
    "name": "vendor/package",
    "require": {
        "php": ">=5.3",
        "psr/log": "~1"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\Package\\": "src"
        }
    },
    "error-handling": "PSR-N"
}
```

Root package capable of working with either handling strategy, but opting to
use traditional error handling:

```json
{
    "name": "vendor/project",
    "require": {
        "php": ">=5.3",
        "psr/log": "~1"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\Project\\": "src"
        }
    },
    "config": {
        "use-error-handling": "traditional"
    }
}
```

### 3.3. Solution B: Error handling management via existing Composer features

This solution involves implementing error handling management by harnessing the
existing Composer dependency resolution system and [metapackages]. This solution
could be implemented with very little effort (at least in terms of development).

Package developers would add special metapackages to their Composer
configuration's [require] section to specify their error handling requirements.
Two metapackages would be defined, and published to [Packagist] by the FIG:
`psr/error-exceptions` and `psr/traditional-errors`.

- Requiring `psr/error-exceptions` would indicate that the package expects a
  `PSR-N` conformant error handler to be installed, where `PSR-N` is the PSR
  number of the error handler specification associated with this meta document.
- Requiring `psr/traditional-errors` would indicate that the package expects the
  error handler to behave in the same manner as the built-in PHP handler.
- Requiring neither of the metapackages would implicitly indicate that the
  package is capable of functioning under either error handling strategy.

Root package developers would specify the type of error handling strategy in use
by adding one of the metapackages to their Composer configuration's [provide]
section.

- Providing `psr/error-exceptions` would indicate that a `PSR-N` conformant
  error handler will be installed.
- Providing `psr/traditional-errors` would indicate that the installed error
  handler will behave like the in-built PHP handler.

It is then up to the root package developer to make good on their guarantee, and
install any appropriate error handler implementations.

During the normal process of dependency resolution, components that require
incompatible error handling strategies would be highlighted as conflicts. This
brings the problem to the attention of the package developer, and allows them to
make informed decisions about how to address the conflict.

If root package developers need to ignore conflicts, they can simply provide
both `psr/error-exceptions` and `psr/traditional-errors`.

#### 3.3.1. Pros

- Little effort is required to implement.
- Requires no new Composer features.

#### 3.3.2. Cons

- Error handling requirements are not expressed as clearly as solution A.
- Stating that a package provides a particular handler is a weak guarantee. The
  actual handler installation becomes the responsibility of the developer,
  making it more prone to human error.
- Conflict messages produced by Composer may be unclear.

#### 3.3.3. Example package configurations

Package requiring PSR-N error handling:

```json
{
    "name": "vendor/package",
    "require": {
        "php": ">=5.3",
        "psr/error-exceptions": "*",
        "psr/log": "~1"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\Package\\": "src"
        }
    }
}
```

Root package providing PSR-N error handling:

```json
{
    "name": "vendor/project",
    "require": {
        "php": ">=5.3",
        "psr/log": "~1"
    },
    "provide": {
        "psr/error-exceptions": "1.0.0"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\Project\\": "src"
        }
    }
}
```

Root package providing traditional error handling:

```json
{
    "name": "vendor/project",
    "require": {
        "php": ">=5.3",
        "psr/log": "~1"
    },
    "provide": {
        "psr/traditional-errors": "1.0.0"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\Project\\": "src"
        }
    }
}
```

## 4. Justification for design decisions

*TBD*

### 4.1. Why error severity is a poor metric

*TBD*

## 5. Best practices going forward

*TBD*

<!-- References -->

[Composer]: https://getcomposer.org/
[config]: https://getcomposer.org/doc/04-schema.md#config
[ErrorException]: http://php.net/manual/en/class.errorexception.php
[metapackages]: https://getcomposer.org/doc/04-schema.md#type
[Packagist]: https://packagist.org/
[provide]: https://getcomposer.org/doc/04-schema.md#provide
[require]: https://getcomposer.org/doc/04-schema.md#require
