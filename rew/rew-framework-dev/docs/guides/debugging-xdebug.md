# [XDEBUG - Debugger and Profiler for PHP](https://xdebug.org/debugging-xdebug.md) 

> Xdebug is a PHP extension which provides debugging and profiling capabilities.  
> Xdebug provides:
>  1. capabilities to debug your scripts interactively 
>  2. profiling information for PHP scripts
>  3. code coverage analysis
 
## Tools you need
 
["Xdebug helper" for Chrome Browser](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc)
must have tool to start using Xdebug and initiate it.  
[PHPStorm](https://www.jetbrains.com/phpstorm/) - All further instruction is based on this IDE but you may use other editors as well.


[Sublime Text 3](https://www.sublimetext.com/) - editor that also support Xdebug.  
[Atom](https://atom.io/)  with [php-debug](https://atom.io/packages/php-debug) - debugging package  
[Visual Studio Code](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug) - By using adapter [(PHP Debug Adapter)](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug)

[Other clients list](https://xdebug.org/docs/remote)-->Clients that support Xdebug.
## Installation

**Remote Debugging DEV servers** - Xdebug PHP extension is pre-installed on all REW dev server starting from v4.6. 
This is recommended option till we have a local development environment.  

**Local Debugging** - you can install Xdebug [locally](https://xdebug.org/docs/install), use Docker or Vagrant image with Xdebug configured.

## Configuration

There are two articles in Confluence for configuring IDE to use xdebug for remote debugging.  

[How To - Setup and use Xdebug (10 min read)](https://realestatewebmasters.atlassian.net/wiki/spaces/PMO/pages/45193346/) by AlexT - based on [Zero-configuration Web Application Debugging with Xdebug and PhpStorm](https://confluence.jetbrains.com/display/PhpStorm/Zero-configuration+Web+Application+Debugging+with+Xdebug+and+PhpStorm)

[Remote Debugging with Xdebug & PHPStorm](https://realestatewebmasters.atlassian.net/wiki/spaces/DFA/pages/47300883/) - by Will H -  Feeds related


## Using Xdebug

After you configured everything an can setup a breakpoint and start debugging you can check this useful articles how to do it and what tools you have in your hand now. 

[Using the PhpStorm Debugger (20 mins read)](https://confluence.jetbrains.com/display/PhpStorm/Using+the+PhpStorm+Debugger#UsingthePhpStormDebugger-1.SetaBreakpoint) - The article covers main features of debugger tool and how to use them.  

[Debugging PHP with PhpStorm - Youtube (1 hour video)](https://www.youtube.com/watch?v=LUTolQw8K9A) -  JetBrains cover of using PHPStorm debugger


## Tips & tricks

1. Lynda course [Debugging PHP Advanced Techniques](https://www.lynda.com/PHP-tutorials/Debugging-PHP-Advanced-Techniques/112414-2.html) -  you can get free access to the course by using [Vancouver public library access](https://www.vpl.ca/extDB/login.remoteDB_Ly?LyndaDotCom) or [Vancouver Island library](http://virl.bc.ca/elibrary/resource/lynda).  
2. Logs are stored in 'dev.your_dev_server_folder.domain.com/log/xdebug' - you can find some useful information there if there is some issue or something not working
3. If debug does not stop on line break you added -  Try use PHPStorm Preferences > Languages and Frameworks > PHP > Debug page ---> External connections ---> Break at first line (checkbox) - then you can trace from the first line of the script and go through files. Or your code is not called at all and need to check bindings etc. 
