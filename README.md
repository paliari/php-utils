# php-utils

#### Installation
	
	$ composer require paliari/php-utils

#### Usage

##### Bump
	$ vendor/bin/bump -v <path|minor|major|prerelase|custom_version> -f <custom_composer.json> -g
	Or
	$ vendor/bin/bump --version <version> --file <custom_composer.json> --git
	Or
	$ vendor/bin/bump -v minor --git
	Or
	$ vendor/bin/bump --version major -f composer_custom.json -g

##### Array

```php
    $a1 = ['a' => 1];
    $a2 = ['b' => ['b1' => 2]];
    $a3 = \Paliari\Utils\A::merge($a1, $a2);
    var_export($a3);
    // export to ['a' => 1, 'b' => ['b1' => 2]]
    
    $val = \Paliari\Utils\A::deepKey($a3, 'b.b1');
    var_export($val);
    // export to 2;

    $flatten = \Paliari\Utils\A::flatten($3);
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


### Authors

- [Marcos Paliari](http://paliari.com.br)
