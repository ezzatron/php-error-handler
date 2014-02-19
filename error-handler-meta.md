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

### 3.2. Solution A: Error handling as a first-class Composer feature

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
project-only section (`config`) of the Composer package configuration schema.
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
allow package developers to ignore these conflicts.

### 3.3. Solution B: Error handling specification through Composer meta-packages

## 4. Justification for design decisions

### 4.1. Why error severity is a poor metric

## 5. Best practices going forward

<!-- References -->

[Composer]: https://getcomposer.org/
[ErrorException]: http://php.net/manual/en/class.errorexception.php
