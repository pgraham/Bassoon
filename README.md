# Bassoon

## Intro

Bassoon is a remote service proxy generator for PHP.  It will take as input the
name of a specially annotated PHP class and generate a Javascript file which
provides a client interface to a service defined by the class.  The generated
javascript requires jQuery and the jQuery Cookie plugin.

## Definition

Defining a remote service is as simple as creating a class with a parameterless
constructor (or no constructor) and using the \bassoon\RemoteService class to
generate the proxy and dispatcher code. 

    class MyService {

        public function doSomething() {
            // ...
        }
    }
    
    // ...
    require_once '/path/to/bassoon/src/Autoloader.php';
    $srvc = new \bassoon\RemoteService('MyService');
    $srvc->generate($proxyOut, $dispatchOut, $dispatchWeb);

In the last line the $proxyOut, $dispatchOut and $dispatchWeb parameters are
important.  Here is an example:

  /var/www/mysite.com
  \_ htdocs
     \- index.php
     \_ js
     \_ ajx
     \_ ...
  \_ ...

Assuming index.php in accessed at http://mysite.com/ then values are:

    $proxyOut = '/var/www/mysite.com/htdocs/js';
    $dispatchOut = '/var/www/mysite.com/htdocs/ajx';
    $dispatchWeb = '/ajx';
  
