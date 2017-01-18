# php-utils

#### Installation
	
	$ composer require paliari/php-utils

#### Usage

##### Bump

Increment version of composer.json.

Params:

- **--git** or **-g** Commit composer.json bumped and create the git tag
- **--version** or **-v** Version type <major | minor | patch | prerelase> or custom version <1.0.0-pre1>. Default is patch
- **--file** or **-f** Composer json file <composer.json>. Default is composer.json
   
   
		$ vendor/bin/bump -v major -f custom_composer.json -g
		Or
		$ vendor/bin/bump --version 1.0.0 --file custom_composer.json --git
		Or
		$ vendor/bin/bump -v minor --git

##### Array
Array facades.

```php
    $a1 = ['a' => 1];
    $a2 = ['b' => ['b1' => 2]];
    $a3 = \Paliari\Utils\A::merge($a1, $a2);
    var_export($a3);
    // export to ['a' => 1, 'b' => ['b1' => 2]]
    
    $val = \Paliari\Utils\A::deepKey($a3, 'b.b1');
    var_export($val);
    // export to 2;

    $flatten = \Paliari\Utils\A::flatten($a3);
    var_export($flatten);
    // export to [1, 2];

    
```

##### Function
```php
    // explode string of space, aliases to explode(' ', $str);
    $str = 'a b 1.3d a-x';
    $a = w($str);
    var_export($a);
    // export to ['a', 'b', '1.3d', 'a-x'];
    
```

##### Logger
Write log to file

```php
    
    // config custom file log, default is realpath(sys_get_temp_dir()) . '/php-util.log'
    $file = __DIR__.'/tmp/logs/test.log';
    \Paliari\Utils\Logger::file($file); 
    
    //Methods avaliables: "critical, error, warning, notice, info, debug".
    
    // info 
    \Paliari\Utils\Logger::info('Yor custom message.');
    
```

##### CatchFatalError

```php
    
    // Init
    \Paliari\Utils\CatchFatalError::init();
    // Or
    \Paliari\Utils\CatchFatalError::init(function ($e) {
        // ... custom handler actions, ex: send mail, save custom log...
        return $e['message'];
    });
    
```


### Authors

- [Marcos Paliari](http://paliari.com.br)
