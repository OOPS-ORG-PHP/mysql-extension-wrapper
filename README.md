Wrapper for PHP mysql extension
===============================
[![GitHub license](https://img.shields.io/badge/license-BSD-blue.svg?style=plastic)](https://raw.githubusercontent.com/OOPS-ORG-PHP/mysql-extension-wrapper/master/LICENSE)

The mysql-wrapper api support mysql extension api, and was designed to work best as with mysql extension.
If you have PHP7 environment and must need mysql extension api, this is good choise.


## License
BSD 2-clause

## Requirements

1. This wrapper api requires mysqli extension on PHP 4.1 and after
2. check with ***is_resource()*** about mysql link and mysql result, replace ***is_myresource()*** api. For example:
```php
    <?php

    # old code
    $con = mysql_connect();
    if ( ! is_resource($con) ) {
        die ("connect failed\n");
    }

    # wrapper code
    $con = mysql_connect();
    if ( ! is_myresource($con) ) {
        die ("connect filed\n");
    }
    ?>
```

## Example
```php
<?php
# even if loaded mysql extension, well done.
require_once 'mysql-wrapper.php';

$con = @mysql_connect ('localhost', 'user', 'pass');
if ( ! is_myresource ($con) ) {
	trigger_error(sprintf('Connect error: %s', mysql_error()), E_USER_ERROR);
	exit;
}

mysql_select_db('mysql', $con);
mysql_set_charset ('utf8', $con);

$result = mysql_query ('SELECT * FROM user', $con);
if ( ! is_myresource($result) ) {
	trigger_error(sprintf('Query Error: %s', mysql_error()), E_USER_WARNING);
}

$rno = mysql_num_rows($result);

while ( ($row = mysql_fetch_object($result)) ) {
	printf("User: %s, Host: %s\n", $row->user, $row->host);
}

mysql_free_result($result);
mysql_close($con);

?>
```

## Composer

first, make composer.json as follow:
```json
{
    "require": {
        "joungkyun/mysql-extension-wrapper": "1.0.*"
    }
}
```

and, install ***mysql-extension-wrapper***

```bash
[user@host project]$ php composer.phpt install
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Installing joungkyun/mysql-extension-wrapper (1.0.1): Downloading (100%)
Writing lock file
Generating autoload files
[user@host project]$
```

and, write code as follow:

```php
<?php
require_once 'vendor/autoload.php';

echo 'mysql_connect is supported ';
if ( function_exists('mysql_connect') )
    echo 'YES';
else
    echo 'NO';

echo "\n";
?>
```


## Credits
JoungKyun.Kim
