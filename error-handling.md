# Error handling

This document describes standard behaviors for PHP error handlers. In addition,
it details a means by which projects can specify their error handling
requirements.

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [RFC 2119].

## 1. Specification

### 1.1. Concepts

- Two standard error handling methods are defined in this document. 'error
  exception' handling and 'legacy' handling.
- It is RECOMMENDED that new code be written to expect the 'Exception' error
  handling method.

### 1.x. Error exception handling

### 1.x. Legacy error handling

<!-- References -->

[RFC 2119]: http://tools.ietf.org/html/rfc2119
