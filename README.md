Wrapper for PHP mysql extension
===============================

The mysql-wrapper api support mysql extension api, and was designed to work best as with mysql extension.


## License
BSD 2-clause

## Requirements

1. This wrapper api requires mysqli extension over PHP 4
2. check with is_resource() about mysql lind and mysql result, replace is_myresource() api. For example:
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

$rno = mysql_num_rows($con);

while ( ($row = mysql_fetch_object($result)) ) {
	printf("User: %s, Host: %s\n", $row->user, $row->host);
}

mysql_free_result($result);
mysql_close($con);

?>
```

## Credits
JoungKyun.Kim
