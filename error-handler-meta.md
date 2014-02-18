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

<!-- References -->

[ErrorException]: http://php.net/manual/en/class.errorexception.php
