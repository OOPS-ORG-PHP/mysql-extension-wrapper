<?php
/*
 * This is deprecated MySQL API wrapper
 *
 * Author: JoungKyun.Kim <http://oops.org>
 * Copyright (c) 2016 JoungKyun.Kim
 * License: BSD 2-Clause
 *
 * $Id: $
 *
 * Warning!
 *
 * 1. You must enable mysqli extension.
 * 2. PHP 4.1 <= PHP VERSION <= PHP7
 * 2. If you check return value by mysql_connect and mysql_result with is_resource(),
 *    you must change to is_myresource() from is_reosource().
 *
 */

// {{{ +-- (bool) is_myresource ($o)
function is_myresource ($o, $onlyres = false) {
	if ( extension_loaded ('mysql') ) {
		if ( ! is_resource ($o) )
			return false;

		$cname = get_resource_type ($o);
		if ( $cname != 'mysql link' && $cname != 'mysql result' )
			return false;

		if ( $onlyres && $cname != 'mysql result' )
			return false;

		unset ($cname);
		return true;
	}

	if ( ! is_object ($o) )
		return false;

	$cname = get_class ($o);
	if ( $cname != 'mysqli' && $cname != 'mysqli_result' )
		return false;

	if ( $onlyres && $cname != 'mysqli_result' )
		return false;

	unset ($cname);
	return true;
}
// }}}

if ( ! function_exists ('mysql_connect') ) {

if ( ! extension_loaded ('mysqli') ) {
	throw new Exception (E_ERROR, 'MySQL wrapper must need mysqli extension');
}

if ( ! function_exists ('___ini_get') ) {
	function ___ini_get ($v) { return ini_get ($v); }
}

$_MySQLCON_ = null;
$_MyConnErr_ = null;

// {{{ Set MySQL Constants
define ('MYSQL_CLIENT_COMPRESS',     MYSQLI_CLIENT_COMPRESS);
define ('MYSQL_CLIENT_IGNORE_SPACE', MYSQLI_CLIENT_IGNORE_SPACE);
define ('MYSQL_CLIENT_INTERACTIVE',  MYSQLI_CLIENT_INTERACTIVE);
define ('MYSQL_CLIENT_SSL',          MYSQLI_CLIENT_SSL);
define ('MYSQL_ASSOC',               MYSQLI_ASSOC);
define ('MYSQL_BOTH',                MYSQLI_BOTH);
define ('MYSQL_NUM',                 MYSQLI_NUM);

if ( ! defined ('MYSQLI_NO_DEFAULT_VALUE_FLAG') )
	define ('MYSQLI_NO_DEFAULT_VALUE_FLAG', -1);

if ( ! defined ('MYSQLI_ON_UPDATE_NOW_FLAG') )
	define ('MYSQLI_ON_UPDATE_NOW_FLAG', -1);

$msyql_filed_flags = array (
	MYSQLI_NOT_NULL_FLAG,
	MYSQLI_PRI_KEY_FLAG,
	MYSQLI_UNIQUE_KEY_FLAG,
	MYSQLI_MULTIPLE_KEY_FLAG,
	MYSQLI_BLOB_FLAG,
	MYSQLI_UNSIGNED_FLAG,
	MYSQLI_ZEROFILL_FLAG,
	MYSQLI_AUTO_INCREMENT_FLAG,
	MYSQLI_TIMESTAMP_FLAG,
	MYSQLI_SET_FLAG,
	MYSQLI_NUM_FLAG,
	MYSQLI_PART_KEY_FLAG,
	MYSQLI_GROUP_FLAG,
	MYSQLI_ENUM_FLAG,
	MYSQLI_BINARY_FLAG,
	MYSQLI_NO_DEFAULT_VALUE_FLAG,
	MYSQLI_ON_UPDATE_NOW_FLAG
);

$mysql_data_type_hash = array(
	0   => 'real',       // decimal
	1   => 'int',        // tiny int
	2   => 'int',        // smallint
	3   => 'int',        // int
	4   => 'real',       // float
	5   => 'real',       // double
	6   => 'null',       // null
	7   => 'timestamp',  // timestamp
	8   => 'int',        // bigint
	9   => 'int',        // mediumint
	10  => 'date',       // date
	11  => 'time',       // time
	12  => 'datetime',   // datetime
	13  => 'year',       // year
	14  => 'date',       // newdate
	15  => 'string',     // varchar
	16  => 'int',        // bit
	246 => 'real',       // newdecimal
	247 => 'string',     // enum
	248 => 'string',     // set
	249 => 'blob',       // tibyblob
	250 => 'blob',       // mediumblob
	251 => 'blob',       // longblob
	252 => 'blob',       // blob
	253 => 'string',     // string
	254 => 'string',     // string
	246 => 'real'        // decimal
);
// }}}


// {{{ +-- (int) mysql_affected_rows (&$c = null)
function mysql_affected_rows (&$c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;
	return mysqli_affected_rows ($c);
}
// }}}

// {{{ +-- (string) mysql_client_encoding (&$c = null)
function mysql_client_encoding (&$c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;
	return mysqli_character_set_name ($c);
}
// }}}

// {{{ +-- (bool) mysql_close (&$c = null)
function mysql_close (&$c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;
	return mysqli_close ($c);
}
// }}}

// {{{ +-- (mysqli_object) mysql_connect ($host = null, $user = null, $pass = null, $link = false, $flag = 0)
function mysql_connect ($host = null, $user = null, $pass = null, $link = false, $flag = 0) {
	$GLOBALS['_MyConnErr_'] = null;

	if ( $host === null )
		$host = ___ini_get ('mysqli.default_host');
	if ( $user === null )
		$user = ___ini_get ('mysqli.default_user');
	if ( $pass === null )
		$pass = ___ini_get ('mysqli.default_pw');

	$persistant = false;
	$socket = null;
	$port = ___ini_get ('mysqli.default_port');

	if ( $host[0] === ':' ) {
		$socket = substr ($host, 1);
		$host = 'localhost';
	} else {
		if ( preg_match ('/^p:/', $host) ) {
			$persistant = true;
			$host = substr ($host, 2);
		}

		if ( preg_match ('/^([^:]+):([\d]+)$/', $host, $m) ) {
			$host = $m[1];
			$port = $m[2];
		}
	}

	if ( $persistant === true )
		$host = 'p:' . $host;

	$c = mysqli_connect ($host, $user, $pass, '', $port, $socket);
	$GLOBALS['_MyConnErr_'] = error_get_last ();

	#if ( $GLOBALS['_MySQLCON_'] === null && is_myresource ($c) )
	if ( is_myresource ($c) )
		$GLOBALS['_MySQLCON_'] = &$c;

	return $c;
}
// }}}

// {{{ +-- (bool) mysql_create_db ($name, $c = null)
function mysql_create_db ($name, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;

	$name = trim ($name);

	if ( ! $name )
		return false;

	return mysqli_query ($c, 'CREATE DATABASE ' . $name);
}
// }}}

// {{{ +-- (bool) mysql_createdb ($name, $c = null)
function mysql_createdb ($name, $c = null) {
	return mysql_create_db ($name, $c);
}
// }}}

// {{{ +-- (bool) mysql_data_seek ($result, $offset)
function mysql_data_seek ($result, $offset) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $offset < 0 || $offset >= $result->num_rows ) {
		$msg = sprintf ('Unable to jump to row %ld on MySQL result index %d', $offset, $result);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	return mysqli_data_seek ($result, $offset);
}
// }}}

// {{{ +-- (string) mysql_db_name ($result, $row, $field = 'Database')
function mysql_db_name ($result, $row, $field = 'Database') {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $row < 0 || $row >= $result->num_rows ) {
		$msg = sprintf ('Unable to jump to row %ld on MySQL result index %d', $row, $result);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	mysqli_data_seek ($result, $row);
	if ( ! ($res = mysqli_fetch_object ($result)) )
		return false;

	return $res->{$field};
}
// }}}

// {{{ +-- (mysqli_result_object|bool) mysql_db_query ($db, $query, $c = null)
function mysql_db_query ($db, $query, $c = null) {
	if ( ($c = mysql_global_resource ($c, 3 - func_num_args ())) == null )
		return;

	$curdb = mysql_get_current_database ($c);
	if ( mysqli_select_db ($c, $db) === false )
		return false;

	$r = mysqli_query ($c, $query);

	if ($curdb !== null)
		mysqli_select_db($c, $curdb);

	return $r;
}
// }}}

// {{{ +-- (bool) mysql_drop_db ($db, $c = null)
function mysql_drop_db ($db, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;

	$db = trim ($db);
	if ( ! $db )
		return false;

	return mysqli_query ($c, sprintf ('DROP DATABASE %s', $db));
}
// }}}

// {{{ +-- (int) mysql_errno ($c = null)
function mysql_errno ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args (), true)) == null )
		return -1;

	return mysqli_errno ($c);
}
// }}}

// {{{ +-- (string) mysql_error ($c = null)
function mysql_error ($c = null) {
	$c = mysql_global_resource ($c, 1 - func_num_args (), true);

	if ( ! is_myresource ($c) ) {
		if ( is_array ($GLOBALS['_MyConnErr_']) && trim ($GLOBALS['_MyConnErr_']['message']) ) {
			preg_match ('/[a-z_]+\(\):\s+\([^)]+\):\s+(.+)/i', $GLOBALS['_MyConnErr_']['message'], $msg);
			return $msg[1];
		}
		return null;
	}

	return mysqli_error ($c);
}
// }}}

// {{{ +-- (string) mysql_escape_string ($escape)
function mysql_escape_string ($escape) {
	return mysqli_real_escape_string ($GLOBALS['_MySQLCON_'], $escape);
}
// }}}

// {{{ +-- (array) mysql_fetch_array ($result, $type = MYSQLI_BOTH)
function mysql_fetch_array ($result, $type = MYSQLI_BOTH) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	$r = mysqli_fetch_array ($result, $type);
	if ( $r === null )
		$r = false;

	return $r;
}
// }}}

// {{{ +-- (array) mysql_fetch_assoc ($result)
function mysql_fetch_assoc ($result) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	return mysql_fetch_array ($result, MYSQLI_ASSOC);
}
// }}}

// {{{ +-- (object) mysql_fetch_field ($result, $offset = null)
function mysql_fetch_field ($result, $offset = null) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $offset !== null ) {
		if ( $offset < 0 || $offset >= $result->field_count ) {
			$msg = sprintf ('Unable to jump to field %ld on MySQL result index %d', $offset, $result);
			trigger_error (
				mysql_trigger_msg ($msg, current (debug_backtrace ())),
				E_USER_WARNING
			);
			return false;
		}
		$res = mysqli_fetch_field_direct ($result, $offset);
	} else
		$res = mysqli_fetch_field ($result);

	if ( $res === false )
		return false;

	$r = (object) array (
		'name'         => $res->name,
		'table'        => $res->table,
		'def'          => '', // default value
		'max_length'   => $res->max_length,
		'not_null'     => ($res->flags & MYSQLI_NOT_NULL_FLAG) ? 1 : 0,
		'primary_key'  => ($res->flags & MYSQLI_PRI_KEY_FLAG) ? 1 : 0,
		'unique_key'   => ($res->flags & MYSQLI_UNIQUE_KEY_FLAG) ? 1 : 0,
		'multiple_key' => ($res->flags & MYSQLI_MULTIPLE_KEY_FLAG) ? 1 : 0,
		'numeric'      => ($res->flags & MYSQLI_NUM_FLAG) ? 1 : 0,
		'blob'         => ($res->flags & MYSQLI_BLOB_FLAG) ? 1 : 0,
		'type'         => $GLOBALS['mysql_data_type_hash'][$res->type],
		'unsigned'     => ($res->flags & MYSQLI_UNSIGNED_FLAG) ? 1 : 0,
		'zerofill'     => ($res->flags & MYSQLI_ZEROFILL_FLAG) ? 1 : 0 

	);

	// exception
	if ( $res->flags === 0 ) // decimal type
		$r->numeric = 1;

	if ( $r->type == 'timestamp' )
		$r->numeric = 1;

	return $r;
}
// }}}

// {{{ +-- (array) mysql_fetch_lengths ($result)
function mysql_fetch_lengths ($result) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	return mysqli_fetch_lengths ($result);
}
// }}}

// {{{ +-- (object) mysql_fetch_object ($result, $classname = null, $params = null)
function mysql_fetch_object ($result, $classname = null, $params = null) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $classname !== null ) {
		if ( ! class_exists ($classname) ) {
			$msg = sprintf ('Class \'%s\' not found', $classname);
			trigger_error (
				mysql_trigger_msg ($msg, current (debug_backtrace ())),
				E_USER_WARNING
			);
			return false;
		}

		if ( $params !== null ) {
			if ( ! is_array ($params) ) {
				$msg = 'Argument 3 passed to mysql_fetch_object() must be of the type array';
				trigger_error (
					mysql_trigger_msg ($msg, current (debug_backtrace ())),
					E_USER_WARNING
				);
				return false;
			}
			$r = mysqli_fetch_object ($result, $calassname, $params);
		} else
			$r = mysqli_fetch_object ($result, $calassname);
	} else
		$r = mysqli_fetch_object ($result);

	return ($r === null) ? false : $r;
}
// }}}

// {{{ +-- (array) mysql_fetch_row ($result)
function mysql_fetch_row ($result) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	$r = mysqli_fetch_row ($result);
	return ($r === null ) ? false : $r;
}
// }}}

// {{{ +-- (string) mysql_field_flags ($result, $offset)
function mysql_field_flags ($result, $offset) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $offset < 0 || $offset >= $result->field_count ) {
		$msg = sprintf ('Unable to jump to field %ld on MySQL result index %d', $offset, $result);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( ($r = mysqli_fetch_field_direct ($result, $offset)) === false ) {
		return false;
	}

	$res = false;
	foreach ( $GLOBALS['msyql_filed_flags'] as $flag ) {
		#printf ("#### {$r->flags} : {$flag} => %d\n", ($r->flags & $flag));
		if ( ! ($r->flags & $flag) )
			continue;

		switch ( $flag ) {
			case MYSQLI_NOT_NULL_FLAG :
				$res .= 'not_null '; break;
			case MYSQLI_PRI_KEY_FLAG :
				$res .= 'primary_key '; break;
			case MYSQLI_UNIQUE_KEY_FLAG :
				$res .= 'unique_key '; break;
			case MYSQLI_MULTIPLE_KEY_FLAG :
				$res .= 'multiple_key '; break;
			case MYSQLI_BLOB_FLAG :
				$res .= 'blob '; break;
			case MYSQLI_UNSIGNED_FLAG :
				$res .= 'unsigned '; break;
			case MYSQLI_ZEROFILL_FLAG :
				$res .= 'zerofill '; break;
			case MYSQLI_AUTO_INCREMENT_FLAG :
				$res .= 'auto_increment '; break;
			case MYSQLI_TIMESTAMP_FLAG :
				$res .= 'timestamp '; break;
			case MYSQLI_SET_FLAG :
				$res .= 'set '; break;
			//case MYSQLI_NUM_FLAG :
			//	$res .= 'numeric '; break;
			case MYSQLI_PART_KEY_FLAG :
				$res .= 'part_key '; break;
			//case MYSQLI_GROUP_FLAG :
			//	$res .= 'group '; break;
			case MYSQLI_ENUM_FLAG :
				$res .= 'enum '; break;
			case MYSQLI_BINARY_FLAG :
				$res .= 'binary '; break;
			//case MYSQLI_NO_DEFAULT_VALUE_FLAG :
			//	$res .= 'no_default_value '; break;
			case MYSQLI_ON_UPDATE_NOW_FLAG :
				$res .= 'on_update_now '; break;
		}
	}


	return rtrim ($res);
}
// }}}

// {{{ +-- (int) mysql_field_len ($result, $offest)
function mysql_field_len ($result, $offest) {
	if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
		return false;

	return $r->length;
}
// }}}

// {{{ +-- (string) mysql_field_name ($result, $offset)
function mysql_field_name ($result, $offset) {
	if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
		return false;

	return $r->name;
}
// }}}

// {{{ +-- (bool) mysql_field_seek ($result, $offset)
function mysql_field_seek ($result, $offset) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( ! isset ($offset) ) {
		$msg = sprintf ('expects parameter 2 to be offset value');
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $offset < 0 || $offset >= mysqli_num_fields ($result) ) {
		$msg = sprintf ('Unable to jump to field %ld on MySQL result index %d', $offset, $result);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	return mysqli_field_seek ($result, $offset);
}
// }}}

// {{{ +-- (stinrg) mysql_field_table ($result, $offset)
function mysql_field_table ($result, $offset) {
	if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
		return false;

	return $r->table;
}
// }}}

// {{{ +-- (string) mysql_field_type ($result, $offset)
function mysql_field_type ($result, $offset) {
	if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
		return false;

	return $GLOBALS['mysql_data_type_hash'][$r->type];
}
// }}}

// {{{ +-- (bool) mysql_free_result ($result)
function mysql_free_result ($result) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	mysqli_free_result ($result);
	return true;
}
// }}}

// {{{ +-- (string) mysql_get_client_info (void)
function mysql_get_client_info () {
	return mysqli_get_client_info ($GLOBALS['_MySQLCON_']);
}
// }}}

// {{{ +-- (string) mysql_get_host_info ($c = null)
function mysql_get_host_info ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_get_host_info ($c);
}
// }}}

// {{{ +-- (int) mysql_get_proto_info ($c = null)
function mysql_get_proto_info ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_get_proto_info ($c);
}
// }}}

// {{{ +-- (string) mysql_get_server_info ($c = null)
function mysql_get_server_info ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_get_server_info ($c);
}
// }}}

// {{{ +-- (string) mysql_info ($c = null)
function mysql_info ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_info ($c);
}
// }}}

// {{{ +-- (int) mysql_insert_id ($c = null)
function mysql_insert_id ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_insert_id ($c);
}
// }}}

// {{{ +-- (mysqli_result_object) mysql_list_dbs ($c = null)
function mysql_list_dbs ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_query ($c, 'SHOW DATABASES');
}
// }}}

// {{{ +-- (mysqli_result_object) mysql_list_fields ($db, $table, $c = null)
function mysql_list_fields ($db, $table, $c = null) {
	if ( ($c = mysql_global_resource ($c, 3 - func_num_args ())) == null )
		return;

	$r = mysql_db_query ($db, sprintf ('SELECT * FROM %s LIMIT 0', $table), $c);

	return $r;
}
// }}}

// {{{ +-- (mysqli_result_object) mysql_list_processes ($c = null)
function mysql_list_processes ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_query ($c, 'SHOW PROCESSLIST');
}
// }}}

// {{{ +-- (mysqli_result_object) mysql_list_tables ($database, $c = null)
function mysql_list_tables ($database, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;

	return mysqli_query ($c, sprintf ('SHOW TABLES FROM %s', $database));
}
// }}}

// {{{ +-- (int) mysql_num_fields ($result)
function mysql_num_fields ($result) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}
	return mysqli_num_fields ($result);
}
// }}}

// {{{ +-- (int) mysql_num_rows ($result)
function mysql_num_rows ($result) {
	if ( ! is_myresource ($result, true) ) {
		trigger_error (
			mysql_trigger_msg(
				'supplied resource is not a valid MySQL result resource',
				current (debug_backtrace ())
			),
			E_USER_ERROR
		);
		return false;
	}

	return mysqli_num_rows ($result);
}
// }}}

// {{{ +-- (mysqli_object) mysql_pconnect ($host = null, $user = null, $pass = null, $link = false, $flag = 0)
function mysql_pconnect ($host = null, $user = null, $pass = null, $link = false, $flag = 0) {
	return mysql_connect ('p:' . $host, $user, $pass, $link, $falg);
}
// }}}

// {{{ +-- (bool) mysql_ping ($c = null)
function mysql_ping ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_ping ($c);
}
// }}}

// {{{ +-- (mysqli_result_object|bool) mysql_query ($query, $c = null)
function mysql_query ($query, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;

	return mysqli_query ($c, $query);
}
// }}}

// {{{ +-- (string) mysql_real_escape_string ($escape, $c = null)
function mysql_real_escape_string ($escape, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;

	return mysqli_real_escape_string ($c, $escape);
}
// }}}

// {{{ +-- (string) mysql_result ($result, $row, $field = 0)
function mysql_result ($result, $row, $field = 0) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $row < 0 || $row >= $result->num_rows ) {
		$msg = sprintf ('Unable to jump to row %ld on MySQL result index %d', $row, $result);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $field < 0 || $field >= $result->field_count ) {
		$msg = sprintf ('Unable to jump to field %ld on MySQL result index %d', $field, $result);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	$res = &$result;

	mysqli_data_seek ($res, $row);
	$r = mysqli_fetch_array ($res, MYSQLI_NUM);

	return $r[$field];
}
// }}}

// {{{ +-- (bool) mysql_select_db ($db, $c = null)
function mysql_select_db ($db, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;
	return mysqli_select_db ($c, $db);
}
// }}}

// {{{ +-- (bool) mysql_set_charset ($charset, $c = null)
function mysql_set_charset ($charset, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;

	return mysqli_set_charset ($c, $charset);
}
// }}}

// {{{ +-- (string) mysql_stat ($c = null)
function mysql_stat ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_stat ($c);
}
// }}}

// {{{ +-- (string) mysql_tablename ($result, $offset)
function mysql_tablename ($result, $offset) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			__FUNCTION__, gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $offset < 0 || $offset >= $result->num_rows ) {
		$msg = sprintf ('Unable to jump to row %ld on MySQL result index %d', $offset, $result);
		trigger_error (
			mysql_trigger_msg ($msg, current (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	mysqli_data_seek ($result, $offset);
	$r = mysqli_fetch_array ($result, MYSQLI_NUM);

	return $r[0];
}
// }}}

// {{{ +-- (int) mysql_thread_id ($c = null)
function mysql_thread_id ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	return mysqli_thread_id ($c);
}
// }}}

// {{{ +-- (mixed|mysqli_result_object) mysql_unbuffered_query ($query, $c = null)
function mysql_unbuffered_query ($query, $c = null) {
	if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
		return;

	return mysqli_query ($c, $query, MYSQLI_USE_RESULT);
}
// }}}

/*
 * non mysql extension api
 * for wrappers
 */

// {{{ +-- (string) mysql_get_current_database ($c = null)
function mysql_get_current_database ($c = null) {
	if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
		return;

	$r = mysqli_query ($c, 'SELECT DATABASES() AS curdb');
	if ( ! is_object ($r) )
		return null;


	$row = mysqli_fetch_object ($r);
	return ($row->curdb == 'NULL') ? null : $row->curdb;
}
// }}}

// {{{ +-- (array) mysqli_mysqli_fetch_field_direct ($result, $offset)
function mysqli_mysqli_fetch_field_direct ($result, $offset) {
	if ( ! is_myresource ($result, true) ) {
		$msg = sprintf (
			'expects parameter 1 to be mysql result object, %s given',
			gettype ($result)
		);
		trigger_error (
			mysql_trigger_msg ($msg, next (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( ! isset ($offset) ) {
		$msg = sprintf ('expects parameter 2 to be offset value');
		trigger_error (
			mysql_trigger_msg ($msg, next (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( $offset < 0 || $offset >= $result->field_count ) {
		$msg = sprintf ('Unable to jump to field %ld on MySQL result index %d', $offset, $result);
		trigger_error (
			mysql_trigger_msg ($msg, next (debug_backtrace ())),
			E_USER_WARNING
		);
		return false;
	}

	if ( ($r = mysqli_fetch_field_direct ($result, $offset)) === false )
		return false;

	return $r;
}
// }}}

// {{{ +-- (mysqli_object) mysql_global_resource (&$c)
function mysql_global_resource (&$c, $argc, $noerr = false) {
	if ( $argc < 0 ) {
		trigger_error (
			mysql_trigger_msg('Wrong argument numers', next (debug_backtrace ())),
			E_USER_WARNING
		);
		return null;
	}

	// $c is exists
	if ( $argc == 0 ) {
		if ( ! is_myresource ($c) ) {
			if ( $noerr === false ) {
				trigger_error (
					mysql_trigger_msg ('no MySQL-Link resource supplied', next (debug_backtrace ())),
					E_USER_WARNING
				);
			}
			return null;
		}
	} else {
		$c = &$GLOBALS['_MySQLCON_'];

		if ( ! is_myresource ($c) ) {
			if ( $noerr === false ) {
				trigger_error (
					mysql_trigger_msg ('no MySQL-Link resource supplied', next (debug_backtrace ())),
					E_USER_WARNING
				);
			}
			return null;
		}
	}


	return $c;
}
// }}}

// {{{ +-- (string) trigger_msg ($msg, $tr)
function mysql_trigger_msg ($msg, $tr) {
	return sprintf ('%s: %s in %s on lien %d', $tr['function'], $msg, $tr['file'], $tr['line']);
}
// }}}

}
?>
