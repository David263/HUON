## JSON variant HUON (HUman Object Notation)

This repository contains PHP and JavaScript implementations of HUON, a human-readable yet efficient structured data language. Also contained are PHP and JavaScript testing programs, with about 100 tests. Currently, only conversion from a HUON string to a returned program value is supported. This is sufficient for using HUON to specify human-written program and other configuration parameters, but not for data serialization for storage or transmission. Converting a value to a HUON string will be written if/when HUON catches on and the need is there.

Bug reports are requested, using the Issues feature. Requests and suggestions submitted via Issues will be noted, but may not be adopted in order to keep this data language and its tools simple. Primary development and bug fixes are done outside of GitHub by the developer, and volunteers are not yet needed.

## Overview
```
// Example of HUON string input
{name: string, "with space":"with space"}
```
Both rest-of-line comments (//) and character-oriented comments (/\*\*/) are supported and removed before generating the output value. Mixing the two to suppress comments may fail. Character strings must be quoted using single (') or double (") quotation marks only if they include spaces or certain other characters used in the HUON syntax. There are no escape sequences used in character strings. UTF-8 character encoding is expected but not required.

HUON value strings can be any of the following scalar values:

- Character strings of any length
- integers (numbers) of 1-8 characters in length (larger or negative numbers can be represented by quoted strings)
- the literals true, t, false, f, and null.

They can also be the following vector (structured) values:

- 0-Indexed arrays enclosed in square brackets ([]): [], [a,3]
- Semantic objects enclosed in curly brackets ({}) and containing key/value pairs: {}, {a:3,b:4}

## Notes

- Arrays and objects may be nested to any reasonable depth.
- Arrays and objects may contain a trailing comma (unless an option is set), for ease of editing: [3,4,5,].
- In PHP, objects are represented by class objects, so gettype() returns "object" instead of "array": `$object=(object)['a'=>'b'];`.
- Feel free to download the zip file (Code > Code > Download ZIP), put the files where you wish, and run the test programs (HUON-test-js.html or HUON-test.php) in a browser; for PHP, you must run using a local or remote webserver as usual.
- Find a bug? Please report it using the Issues feature above.
