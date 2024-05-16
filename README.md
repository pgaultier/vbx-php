VB Backports
============

VB Backports is a collection of tools we encountered while modernizing old VB5/6 projects 
We needed to port some of the code to PHP.

Installation
------------

If you use Packagist for installing packages, then you can update your composer.json like this :

``` json
{
    "require": {
        "pgaultier/vbx-php": "*"
    }
}
```

Components backported
---------------------

### Rnd

The PRNG Rnd function available in VB5/6 is not available in PHP.

``` php
$vbRandomizer = new Rnd();

$vbRandomizer->rnd(-1);
$seed = 1500;
$vbRandomizer->randomize($seed);

$randomNumber = $vbRandomizer->rnd();
```

### ClsEncrypt

Original VB code created by Michael Ciurescu (CVMichael from vbforums.com)

https://www.vbforums.com/showthread.php?231798-VB-31-Bit-Encryption-function

```php 
$clsEncrypt = new ClsEncrypt();
$password = 'secret key';
$string = 'Hello World';

$encryptedBinaryString = $clsEncrypt->rndCryptLevel2($string, $password);

$decryptedString = $clsEncrypt->rndDecryptLevel2($encryptedBinaryString, $password);
// $decryptedString == 'Hello World'
```

Contributing
------------

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a [feature branch ](http://nvie.com/posts/a-successful-git-branching-model/), and send us a pull request.