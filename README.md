# Bassoon

## Intro

Bassoon is a remote service proxy generator for PHP.  It will take as input the
name of a specially annotated PHP class and generate a Javascript file which
provides a client interface to a service defined by the class.  The generated
javascript requires jQuery and the jQuery Cookie plugin.

## Service Definition

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

Assuming index.php in accessed at http://mysite.com/ then possible values are:

    // $proxyOut is the path to a file
    $proxyOut = '/var/www/mysite.com/htdocs/js/MyService.js';

    // $dispatchOut is the path to a directory
    $dispatchOut = '/var/www/mysite.com/htdocs/ajx/MyService';

    // $dispatchWeb is the web-accessible path to the directory specified by
    // $dispatchOut
    $dispatchWeb = '/ajx/MyService';


To include the proxy in a page you would now do:

    <script src="/js/MyService.js"></script>
  

## Using a proxy

Once a RemoteService has been included in a page using the code above, the
service can be accessed in javascript code.

    window['MyService'].doSomething(function (data) {
      // Handle service method response ...
    });

  The default behaviour is to create
the proxy in a variable named the same as the service class with backslash
namespace delimiters (\) replaced with underscores (_).  So the proxy for a
service class named \my\name\space\MyService would be accessed with the
javascript name my_name_space_MyService, or using the example above without a
namespace, MyService.  This behaviour can be overridden by specifying a
@Service annotation with a name parameter on the service class:

    /**
     * @Service(name = MyServiceJsProxy)
     */
    class MyService {
        // ...
    }

meaning the service is now accessed as:

    window['MyServiceJsProxy'].doSomething(function (data) {
      // Handle service method response ...
    });


