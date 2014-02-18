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

#### Before exceptions

Before the introduction of exceptions, error conditions in PHP were handled
through *error messages*. These error messages were differentiated by
*severity*; most notably errors, warnings, and notices (and eventually
deprecation messages).

The severity of errors was used to determine whether script execution should
continue after the error was handled, or if execution should halt for a serious
error. The situation was made more complex by the fact that error handling
behavior could vary across different PHP installations due to the 'error
reporting' level being configurable.

In addition to serving as a means for controlling program flow in exceptional
circumstances, the error message system was often harnessed as a cheap way
to implement application-level logging.
