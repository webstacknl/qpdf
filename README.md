# Wrapper for QPDF

This package can be used to merge (some pages of) PDF files into one PDF using the QPDF library.

## Installation
```shell
composer require webstack/qpdf
```

## Code examples

Combine two PDF files into one
```php
<?php

use Webstack\QPDF\QPDF;

require('vendor/autoload.php');

QPDF::createInstance()
  ->addFile('input-1.pdf')
  ->addFile('input-2.pdf')
  ->write('output.pdf');
```

Combine two PDF files into, specifying which pages to use
```php
<?php

use Webstack\QPDF\QPDF;

require('vendor/autoload.php');

QPDF::createInstance()
  ->addPages('input-1.pdf', '1-3')
  ->addPages('input-2.pdf', '4,5')
  ->write('output.pdf');
```

Returns the output instead of writing it to a file
```php
<?php

use Webstack\QPDF\QPDF;

require('vendor/autoload.php');

QPDF::createInstance()
  ->addFile('input-1.pdf')
  ->addFile('input-2.pdf')
  ->output();
```

Returns the number of pages in a file
```php
<?php

use Webstack\QPDF\QPDF;

require('vendor/autoload.php');

QPDF::createInstance()->getNumberOfPages('input-1.pdf');
```

## Tests
To run the tests use the following  command
```shell
vendor/bin/phpunit --bootstrap vendor/autoload.php tests
```

## Prerequisites
This package utilizes the [QPDF](https://qpdf.sourceforge.io/) library and must be installed on te system.

>QPDF is a command-line program that does structural, content-preserving transformations on PDF files. It could have been called something like pdf-to-pdf. It also provides many useful capabilities to developers of PDF-producing software or for people who just want to look at the innards of a PDF file to learn more about how they work.

### QPDF installation instructions

Ubuntu
```shell
sudo apt-get install -y qpdf
```

MacOS (using homebrew)
```shell
brew install qpdf
```