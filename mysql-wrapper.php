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
 *    you must change to is_myresource() from is_resource().
 *
 */

/**
 * @param $o
 * @param bool $onlyres
 * @return bool
 */
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

if ( ! function_exists ('mysql_connect') ) {

    if ( ! extension_loaded ('mysqli') ) {
        throw new Exception (E_ERROR, 'MySQL wrapper must need mysqli extension');
    }

    if ( ! function_exists ('___ini_get') ) {
        function ___ini_get ($v) { return ini_get ($v); }
    }

    $_MySQLCON_ = null;
    $_MyConnErr_ = null;

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



    /**     
     * Get number of affected rows in previous MySQL operation
     * @link http://php.net/manual/en/function.mysql-affected-rows.php
     * @param resource $link_identifier [optional]
     * @return int the number of affected rows on success, and -1 if the last query
     * failed.
     * </p>
     * <p>
     * If the last query was a DELETE query with no WHERE clause, all
     * of the records will have been deleted from the table but this
     * function will return zero with MySQL versions prior to 4.1.2.
     * </p>
     * <p>
     * When using UPDATE, MySQL will not update columns where the new value is the
     * same as the old value. This creates the possibility that
     * <b>mysql_affected_rows</b> may not actually equal the number
     * of rows matched, only the number of rows that were literally affected by
     * the query.
     * </p>
     * <p>
     * The REPLACE statement first deletes the record with the same primary key
     * and then inserts the new record. This function returns the number of
     * deleted records plus the number of inserted records.
     * @since 4.0
     * @since 5.0
     */
    function mysql_affected_rows (&$c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;
        return mysqli_affected_rows ($c);
    }


    /**     
     * Returns the name of the character set
     * @link http://php.net/manual/en/function.mysql-client-encoding.php
     * @param resource $link_identifier [optional]
     * @return string the default character set name for the current connection.
     * @since 4.3.0
     * @since 5.0
     */
    function mysql_client_encoding (&$c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;
        return mysqli_character_set_name ($c);
    }

    /**     
     * Close MySQL connection
     * @link http://php.net/manual/en/function.mysql-close.php
     * @param resource $link_identifier [optional]
     * @return bool true on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
    function mysql_close (&$c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;
        return mysqli_close ($c);
    }

    /**     
     * Open a connection to a MySQL Server
     * @link http://php.net/manual/en/function.mysql-connect.php
     * @param string $server [optional] <p>
     * The MySQL server. It can also include a port number. e.g.
     * "hostname:port" or a path to a local socket e.g. ":/path/to/socket" for
     * the localhost.
     * </p>
     * <p>
     * If the PHP directive
     * mysql.default_host is undefined (default), then the default
     * value is 'localhost:3306'. In &sqlsafemode;, this parameter is ignored
     * and value 'localhost:3306' is always used.
     * </p>
     * @param string $username [optional] <p>
     * The username. Default value is defined by mysql.default_user. In
     * &sqlsafemode;, this parameter is ignored and the name of the user that
     * owns the server process is used.
     * </p>
     * @param string $password [optional] <p>
     * The password. Default value is defined by mysql.default_password. In
     * &sqlsafemode;, this parameter is ignored and empty password is used.
     * </p>
     * @param bool $new_link [optional] <p>
     * If a second call is made to <b>mysql_connect</b>
     * with the same arguments, no new link will be established, but
     * instead, the link identifier of the already opened link will be
     * returned. The <i>new_link</i> parameter modifies this
     * behavior and makes <b>mysql_connect</b> always open
     * a new link, even if <b>mysql_connect</b> was called
     * before with the same parameters.
     * In &sqlsafemode;, this parameter is ignored.
     * </p>
     * @param int $client_flags [optional] <p>
     * The <i>client_flags</i> parameter can be a combination
     * of the following constants:
     * 128 (enable LOAD DATA LOCAL handling),
     * <b>MYSQL_CLIENT_SSL</b>,
     * <b>MYSQL_CLIENT_COMPRESS</b>,
     * <b>MYSQL_CLIENT_IGNORE_SPACE</b> or
     * <b>MYSQL_CLIENT_INTERACTIVE</b>.
     * Read the section about for further information.
     * In &sqlsafemode;, this parameter is ignored.
     * </p>
     * @return resource a MySQL link identifier on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * @param $name
     * @param null $c
     * @return bool|mysqli_result|void
     */
    function mysql_create_db ($name, $c = null) {
        if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
            return;

        $name = trim ($name);

        if ( ! $name )
            return false;

        return mysqli_query ($c, 'CREATE DATABASE ' . $name);
    }


    /**
     * @param $name
     * @param null $c
     * @return bool|mysqli_result|void
     */
    function mysql_createdb ($name, $c = null) {
        return mysql_create_db ($name, $c);
    }


    /**
     * Move internal result pointer
     * @link http://php.net/manual/en/function.mysql-data-seek.php
     * @param resource $result
     * @param int $row_number <p>
     * The desired row number of the new result pointer.
     * </p>
     * @return bool true on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Retrieves database name from the call to <b>mysql_list_dbs</b>
     * @link http://php.net/manual/en/function.mysql-db-name.php
     * @param resource $result <p>
     * The result pointer from a call to <b>mysql_list_dbs</b>.
     * </p>
     * @param int $row <p>
     * The index into the result set.
     * </p>
     * @param mixed $field [optional] <p>
     * The field name.
     * </p>
     * @return string the database name on success, and false on failure. If false
     * is returned, use <b>mysql_error</b> to determine the nature
     * of the error.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Selects a database and executes a query on it
     * @link http://php.net/manual/en/function.mysql-db-query.php
     
     * @param string $database <p>
     * The name of the database that will be selected.
     * </p>
     * @param string $query <p>
     * The MySQL query.
     * </p>
     * <p>
     * Data inside the query should be properly escaped.
     * </p>
     * @param resource $link_identifier [optional]
     * @return resource a positive MySQL result resource to the query result,
     * or false on error. The function also returns true/false for
     * INSERT/UPDATE/DELETE
     * queries to indicate success/failure.
     * @since 4.0
     * @since 5.0
     */
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


    /**
     * @param $db
     * @param null $c
     * @return bool|mysqli_result|void
     */
    function mysql_drop_db ($db, $c = null) {
        if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
            return;

        $db = trim ($db);
        if ( ! $db )
            return false;

        return mysqli_query ($c, sprintf ('DROP DATABASE %s', $db));
    }

    /**
     * Returns the numerical value of the error message from previous MySQL operation
     * @link http://php.net/manual/en/function.mysql-errno.php
     * @param resource $link_identifier [optional]
     * @return int the error number from the last MySQL function, or
     * 0 (zero) if no error occurred.
     * @since 4.0
     * @since 5.0
     */
    function mysql_errno ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args (), true)) == null )
            return -1;

        return mysqli_errno ($c);
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     * @link http://php.net/manual/en/function.mysql-error.php
     * @param resource $link_identifier [optional]
     * @return string the error text from the last MySQL function, or
     * '' (empty string) if no error occurred.
     * @since 4.0
     * @since 5.0
     */
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


    if(!function_exists('mysql_escape_string')) {
        /**
         * Escapes a string for use in a mysql_query
         * @link http://php.net/manual/en/function.mysql-escape-string.php
         * @param string $unescaped_string <p>
         * The string that is to be escaped.
         * </p>
         * @return string the escaped string.
         * @since 4.0.3
         * @since 5.0
         */
        function mysql_escape_string($escape)
        {
            return mysqli_real_escape_string($GLOBALS['_MySQLCON_'], $escape);
        }
    }

    /**
     * Fetch a result row as an associative array, a numeric array, or both
     * @link http://php.net/manual/en/function.mysql-fetch-array.php
     * @param resource $result
     * @param int $result_type [optional] <p>
     * The type of array that is to be fetched. It's a constant and can
     * take the following values: <b>MYSQL_ASSOC</b>,
     * <b>MYSQL_NUM</b>, and
     * <b>MYSQL_BOTH</b>.
     * </p>
     * @return array an array of strings that corresponds to the fetched row, or false
     * if there are no more rows. The type of returned array depends on
     * how <i>result_type</i> is defined. By using
     * <b>MYSQL_BOTH</b> (default), you'll get an array with both
     * associative and number indices. Using <b>MYSQL_ASSOC</b>, you
     * only get associative indices (as <b>mysql_fetch_assoc</b>
     * works), using <b>MYSQL_NUM</b>, you only get number indices
     * (as <b>mysql_fetch_row</b> works).
     * </p>
     * <p>
     * If two or more columns of the result have the same field names,
     * the last column will take precedence. To access the other column(s)
     * of the same name, you must use the numeric index of the column or
     * make an alias for the column. For aliased columns, you cannot
     * access the contents with the original column name.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Fetch a result row as an associative array
     * @link http://php.net/manual/en/function.mysql-fetch-assoc.php
     * @param resource $result
     * @return array an associative array of strings that corresponds to the fetched row, or
     * false if there are no more rows.
     * </p>
     * <p>
     * If two or more columns of the result have the same field names,
     * the last column will take precedence. To access the other
     * column(s) of the same name, you either need to access the
     * result with numeric indices by using
     * <b>mysql_fetch_row</b> or add alias names.
     * See the example at the <b>mysql_fetch_array</b>
     * description about aliases.
     * @since 4.0.3
     * @since 5.0
     */
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

    /**
     * Get column information from a result and return as an object
     * @link http://php.net/manual/en/function.mysql-fetch-field.php
     * @param resource $result
     * @param int $field_offset [optional] <p>
     * The numerical field offset. If the field offset is not specified, the
     * next field that was not yet retrieved by this function is retrieved.
     * The <i>field_offset</i> starts at 0.
     * </p>
     * @return object an object containing field information. The properties
     * of the object are:
     * </p>
     * <p>
     * name - column name
     * table - name of the table the column belongs to
     * def - default value of the column
     * max_length - maximum length of the column
     * not_null - 1 if the column cannot be null
     * primary_key - 1 if the column is a primary key
     * unique_key - 1 if the column is a unique key
     * multiple_key - 1 if the column is a non-unique key
     * numeric - 1 if the column is numeric
     * blob - 1 if the column is a BLOB
     * type - the type of the column
     * unsigned - 1 if the column is unsigned
     * zerofill - 1 if the column is zero-filled
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Get the length of each output in a result
     * @link http://php.net/manual/en/function.mysql-fetch-lengths.php
     * @param resource $result
     * @return array An array of lengths on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Fetch a result row as an object
     * @link http://php.net/manual/en/function.mysql-fetch-object.php
     * @param resource $result
     * @param string $class_name [optional] <p>
     * The name of the class to instantiate, set the properties of and return.
     * If not specified, a <b>stdClass</b> object is returned.
     * </p>
     * @param array $params [optional] <p>
     * An optional array of parameters to pass to the constructor
     * for <i>class_name</i> objects.
     * </p>
     * @return stdClass|object an object with string properties that correspond to the
     * fetched row, or false if there are no more rows.
     * </p>
     * <p>
     * mysql_fetch_row fetches one row of data from
     * the result associated with the specified result identifier. The
     * row is returned as an array. Each result column is stored in an
     * array offset, starting at offset 0.
     * @since 4.0
     * @since 5.0
     */
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
                );// {{{ +-- (object) mysql_fetch_object ($result, $classname = null, $params = null)
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

    /**
     * Get a result row as an enumerated array
     * @link http://php.net/manual/en/function.mysql-fetch-row.php
     * @param resource $result
     * @return array an numerical array of strings that corresponds to the fetched row, or
     * false if there are no more rows.
     * </p>
     * <p>
     * <b>mysql_fetch_row</b> fetches one row of data from
     * the result associated with the specified result identifier. The
     * row is returned as an array. Each result column is stored in an
     * array offset, starting at offset 0.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Get the flags associated with the specified field in a result
     * @link http://php.net/manual/en/function.mysql-field-flags.php
     * @param resource $result
     * @param int $field_offset
     * @return string a string of flags associated with the result or false on failure.
     * </p>
     * <p>
     * The following flags are reported, if your version of MySQL
     * is current enough to support them: "not_null",
     * "primary_key", "unique_key",
     * "multiple_key", "blob",
     * "unsigned", "zerofill",
     * "binary", "enum",
     * "auto_increment" and "timestamp".
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Returns the length of the specified field
     * @link http://php.net/manual/en/function.mysql-field-len.php
     * @param resource $result
     * @param int $field_offset
     * @return int The length of the specified field index on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
    function mysql_field_len ($result, $offest) {
        if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
            return false;

        return $r->length;
    }

    /**
     * Get the name of the specified field in a result
     * @link http://php.net/manual/en/function.mysql-field-name.php
     * @param resource $result
     * @param int $field_offset
     * @return string The name of the specified field index on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
    function mysql_field_name ($result, $offset) {
        if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
            return false;

        return $r->name;
    }

    /**
     * Set result pointer to a specified field offset
     * @link http://php.net/manual/en/function.mysql-field-seek.php
     * @param resource $result
     * @param int $field_offset
     * @return bool true on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Get name of the table the specified field is in
     * @link http://php.net/manual/en/function.mysql-field-table.php
     * @param resource $result
     * @param int $field_offset
     * @return string The name of the table on success.
     * @since 4.0
     * @since 5.0
     */
    function mysql_field_table ($result, $offset) {
        if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
            return false;

        return $r->table;
    }

    /**
     * Get the type of the specified field in a result
     * @link http://php.net/manual/en/function.mysql-field-type.php
     * @param resource $result
     * @param int $field_offset
     * @return string The returned field type
     * will be one of "int", "real",
     * "string", "blob", and others as
     * detailed in the MySQL
     * documentation.
     * @since 4.0
     * @since 5.0
     */
    function mysql_field_type ($result, $offset) {
        if ( ($r = mysqli_mysqli_fetch_field_direct ($result, $offset)) === false )
            return false;

        return $GLOBALS['mysql_data_type_hash'][$r->type];
    }

    /**
     * Free result memory
     * @link http://php.net/manual/en/function.mysql-free-result.php
     * @param resource $result
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * If a non-resource is used for the result, an
     * error of level E_WARNING will be emitted. It's worth noting that
     * mysql_query only returns a resource
     * for SELECT, SHOW, EXPLAIN, and DESCRIBE queries.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Get MySQL client info
     * @link http://php.net/manual/en/function.mysql-get-client-info.php
     * @return string The MySQL client version.
     * @since 4.0.5
     * @since 5.0
     */
    function mysql_get_client_info () {
        return mysqli_get_client_info ($GLOBALS['_MySQLCON_']);
    }

    /**
     * Get MySQL host info
     * @link http://php.net/manual/en/function.mysql-get-host-info.php
     * @param resource $link_identifier [optional]
     * @return string a string describing the type of MySQL connection in use for the
     * connection or false on failure.
     * @since 4.0.5
     * @since 5.0
     */
    function mysql_get_host_info ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_get_host_info ($c);
    }

    /**
     * Get MySQL protocol info
     * @link http://php.net/manual/en/function.mysql-get-proto-info.php
     * @param resource $link_identifier [optional]
     * @return int the MySQL protocol on success or false on failure.
     * @since 4.0.5
     * @since 5.0
     */
    function mysql_get_proto_info ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_get_proto_info ($c);
    }

    /**
     * Get MySQL server info
     * @link http://php.net/manual/en/function.mysql-get-server-info.php
     * @param resource $link_identifier [optional]
     * @return string the MySQL server version on success or false on failure.
     * @since 4.0.5
     * @since 5.0
     */
    function mysql_get_server_info ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_get_server_info ($c);
    }

    /**
     * Get information about the most recent query
     * @link http://php.net/manual/en/function.mysql-info.php
     * @param resource $link_identifier [optional]
     * @return string information about the statement on success, or false on
     * failure. See the example below for which statements provide information,
     * and what the returned value may look like. Statements that are not listed
     * will return false.
     * @since 4.3.0
     * @since 5.0
     */
    function mysql_info ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_info ($c);
    }

    /**
     * Get the ID generated in the last query
     * @link http://php.net/manual/en/function.mysql-insert-id.php
     * @param resource $link_identifier [optional]
     * @return int The ID generated for an AUTO_INCREMENT column by the previous
     * query on success, 0 if the previous
     * query does not generate an AUTO_INCREMENT value, or false if
     * no MySQL connection was established.
     * @since 4.0
     * @since 5.0
     */
    function mysql_insert_id ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_insert_id ($c);
    }

    /**
     * List databases available on a MySQL server
     * @link http://php.net/manual/en/function.mysql-list-dbs.php
     * @param resource $link_identifier [optional]
     * @return resource a result pointer resource on success, or false on
     * failure. Use the <b>mysql_tablename</b> function to traverse
     * this result pointer, or any function for result tables, such as
     * <b>mysql_fetch_array</b>.
     * @since 4.0
     * @since 5.0
     */
    function mysql_list_dbs ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_query ($c, 'SHOW DATABASES');
    }

    /**
     * List MySQL table fields
     * @link http://php.net/manual/en/function.mysql-list-fields.php
     * @param string $database_name <p>
     * The name of the database that's being queried.
     * </p>
     * @param string $table_name <p>
     * The name of the table that's being queried.
     * </p>
     * @param resource $link_identifier [optional]
     * @return resource A result pointer resource on success, or false on
     * failure.
     * </p>
     * <p>
     * The returned result can be used with <b>mysql_field_flags</b>,
     * <b>mysql_field_len</b>,
     * <b>mysql_field_name</b>&listendand;
     * <b>mysql_field_type</b>.
     * @since 4.0
     * @since 5.0
     */
    function mysql_list_fields ($db, $table, $c = null) {
        if ( ($c = mysql_global_resource ($c, 3 - func_num_args ())) == null )
            return;

        $r = mysql_db_query ($db, sprintf ('SELECT * FROM %s LIMIT 0', $table), $c);

        return $r;
    }

    /**
     * List MySQL processes
     * @link http://php.net/manual/en/function.mysql-list-processes.php
     * @param resource $link_identifier [optional]
     * @return resource A result pointer resource on success or false on failure.
     * @since 4.3.0
     * @since 5.0
     */
    function mysql_list_processes ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_query ($c, 'SHOW PROCESSLIST');
    }

    /**
     * List tables in a MySQL database
     * @link http://php.net/manual/en/function.mysql-list-tables.php
     * @param string $database <p>
     * The name of the database
     * </p>
     * @param resource $link_identifier [optional]
     * @return resource A result pointer resource on success or false on failure.
     * </p>
     * <p>
     * Use the <b>mysql_tablename</b> function to
     * traverse this result pointer, or any function for result tables,
     * such as <b>mysql_fetch_array</b>.
     * @since 4.0
     * @since 5.0
     */
    function mysql_list_tables ($database, $c = null) {
        if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
            return;

        return mysqli_query ($c, sprintf ('SHOW TABLES FROM %s', $database));
    }

    /**
     * Get number of fields in result
     * @link http://php.net/manual/en/function.mysql-num-fields.php
     * @param resource $result
     * @return int the number of fields in the result set resource on
     * success or false on failure.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Get number of rows in result
     * @link http://php.net/manual/en/function.mysql-num-rows.php
     * @param resource $result <p>The result resource that is being evaluated. This result comes from a call to mysql_query().</p>
     * @return int <p>The number of rows in the result set on success or FALSE on failure. </p>
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Open a persistent connection to a MySQL server
     * @link http://php.net/manual/en/function.mysql-pconnect.php
     * @param string $server [optional] <p>
     * The MySQL server. It can also include a port number. e.g.
     * "hostname:port" or a path to a local socket e.g. ":/path/to/socket" for
     * the localhost.
     * </p>
     * <p>
     * If the PHP directive
     * mysql.default_host is undefined (default), then the default
     * value is 'localhost:3306'
     * </p>
     * @param string $username [optional] <p>
     * The username. Default value is the name of the user that owns the
     * server process.
     * </p>
     * @param string $password [optional] <p>
     * The password. Default value is an empty password.
     * </p>
     * @param int $client_flags [optional] <p>
     * The <i>client_flags</i> parameter can be a combination
     * of the following constants:
     * 128 (enable LOAD DATA LOCAL handling),
     * <b>MYSQL_CLIENT_SSL</b>,
     * <b>MYSQL_CLIENT_COMPRESS</b>,
     * <b>MYSQL_CLIENT_IGNORE_SPACE</b> or
     * <b>MYSQL_CLIENT_INTERACTIVE</b>.
     * </p>
     * @return resource a MySQL persistent link identifier on success, or false on
     * failure.
     * @since 4.0
     * @since 5.0
     */
    function mysql_pconnect ($host = null, $user = null, $pass = null, $link = false, $flag = 0) {
        return mysql_connect ('p:' . $host, $user, $pass, $link, $flag);
    }

    /**
     * Ping a server connection or reconnect if there is no connection
     * @link http://php.net/manual/en/function.mysql-ping.php
     * @param resource $link_identifier [optional]
     * @return bool true if the connection to the server MySQL server is working,
     * otherwise false.
     * @since 4.3.0
     * @since 5.0
     */
    function mysql_ping ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_ping ($c);
    }

    /**
     * Send a MySQL query
     * @link http://php.net/manual/en/function.mysql-query.php
     * @param string $query <p>
     * An SQL query
     * </p>
     * <p>
     * The query string should not end with a semicolon.
     * Data inside the query should be properly escaped.
     * </p>
     * @param resource $link_identifier [optional]
     * @return resource For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements returning resultset,
     * <b>mysql_query</b>
     * returns a resource on success, or false on
     * error.
     * </p>
     * <p>
     * For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc,
     * <b>mysql_query</b> returns true on success
     * or false on error.
     * </p>
     * <p>
     * The returned result resource should be passed to
     * <b>mysql_fetch_array</b>, and other
     * functions for dealing with result tables, to access the returned data.
     * </p>
     * <p>
     * Use <b>mysql_num_rows</b> to find out how many rows
     * were returned for a SELECT statement or
     * <b>mysql_affected_rows</b> to find out how many
     * rows were affected by a DELETE, INSERT, REPLACE, or UPDATE
     * statement.
     * </p>
     * <p>
     * <b>mysql_query</b> will also fail and return false
     * if the user does not have permission to access the table(s) referenced by
     * the query.
     * @since 4.0
     * @since 5.0
     */
    function mysql_query ($query, $c = null) {
        if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
            return;

        return mysqli_query ($c, $query);
    }

    if(!function_exists('mysql_real_escape_string')) {
        /**
         * Escapes special characters in a string for use in an SQL statement
         * @link http://php.net/manual/en/function.mysql-real-escape-string.php
         * @param string $unescaped_string <p>
         * The string that is to be escaped.
         * </p>
         * @param resource $link_identifier [optional]
         * @return string the escaped string, or false on error.
         * @since 4.3.0
         * @since 5.0
         */
        function mysql_real_escape_string($escape, $c = null)
        {
            if (($c = mysql_global_resource($c, 2 - func_num_args())) == null) {
                return;
            }

            return mysqli_real_escape_string($c, $escape);
        }
    }

    /**
     * Get result data
     * @link http://php.net/manual/en/function.mysql-result.php
     * @param resource $result
     * @param int $row <p>
     * The row number from the result that's being retrieved. Row numbers
     * start at 0.
     * </p>
     * @param mixed $field [optional] <p>
     * The name or offset of the field being retrieved.
     * </p>
     * <p>
     * It can be the field's offset, the field's name, or the field's table
     * dot field name (tablename.fieldname). If the column name has been
     * aliased ('select foo as bar from...'), use the alias instead of the
     * column name. If undefined, the first field is retrieved.
     * </p>
     * @return string The contents of one cell from a MySQL result set on success, or
     * false on failure.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Select a MySQL database
     * @link http://php.net/manual/en/function.mysql-select-db.php
     * @param string $database_name <p>
     * The name of the database that is to be selected.
     * </p>
     * @param resource $link_identifier [optional]
     * @return bool true on success or false on failure.
     * @since 4.0
     * @since 5.0
     */
    function mysql_select_db ($db, $c = null) {
        if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
            return;
        return mysqli_select_db ($c, $db);
    }

    /**
     * Sets the client character set
     * @link http://php.net/manual/en/function.mysql-set-charset.php
     * @param string $charset <p>
     * A valid character set name.
     * </p>
     * @param resource $link_identifier [optional]
     * @return bool true on success or false on failure.
     * @since 5.2.3
     */
    function mysql_set_charset ($charset, $c = null) {
        if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
            return;

        return mysqli_set_charset ($c, $charset);
    }

    /**
     * Get current system status
     * @link http://php.net/manual/en/function.mysql-stat.php
     * @param resource $link_identifier [optional]
     * @return string a string with the status for uptime, threads, queries, open tables,
     * flush tables and queries per second. For a complete list of other status
     * variables, you have to use the SHOW STATUS SQL command.
     * If <i>link_identifier</i> is invalid, null is returned.
     * @since 4.3.0
     * @since 5.0
     */
    function mysql_stat ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_stat ($c);
    }

    /**
     * Get table name of field
     * @link http://php.net/manual/en/function.mysql-tablename.php
     * @param resource $result <p>
     * A result pointer resource that's returned from
     * <b>mysql_list_tables</b>.
     * </p>
     * @param int $i <p>
     * The integer index (row/table number)
     * </p>
     * @return string The name of the table on success or false on failure.
     * </p>
     * <p>
     * Use the <b>mysql_tablename</b> function to
     * traverse this result pointer, or any function for result tables,
     * such as <b>mysql_fetch_array</b>.
     * @since 4.0
     * @since 5.0
     */
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

    /**
     * Return the current thread ID
     * @link http://php.net/manual/en/function.mysql-thread-id.php
     * @param resource $link_identifier [optional]
     * @return int The thread ID on success or false on failure.
     * @since 4.3.0
     * @since 5.0
     */
    function mysql_thread_id ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        return mysqli_thread_id ($c);
    }

    /**
     * Send an SQL query to MySQL without fetching and buffering the result rows.
     * @link http://php.net/manual/en/function.mysql-unbuffered-query.php
     * @param string $query <p>
     * The SQL query to execute.
     * </p>
     * <p>
     * Data inside the query should be properly escaped.
     * </p>
     * @param resource $link_identifier [optional]
     * @return resource For SELECT, SHOW, DESCRIBE or EXPLAIN statements,
     * <b>mysql_unbuffered_query</b>
     * returns a resource on success, or false on
     * error.
     * </p>
     * <p>
     * For other type of SQL statements, UPDATE, DELETE, DROP, etc,
     * <b>mysql_unbuffered_query</b> returns true on success
     * or false on error.
     * @since 4.0.6
     * @since 5.0
     */
    function mysql_unbuffered_query ($query, $c = null) {
        if ( ($c = mysql_global_resource ($c, 2 - func_num_args ())) == null )
            return;

        return mysqli_query ($c, $query, MYSQLI_USE_RESULT);
    }

    /*
     * non mysql extension api
     * for wrappers
     */

    /**
     * @param null $c
     * @return null|void
     */
    function mysql_get_current_database ($c = null) {
        if ( ($c = mysql_global_resource ($c, 1 - func_num_args ())) == null )
            return;

        $r = mysqli_query ($c, 'SELECT DATABASES() AS curdb');
        if ( ! is_object ($r) )
            return null;


        $row = mysqli_fetch_object ($r);
        return ($row->curdb == 'NULL') ? null : $row->curdb;
    }

    /**
     * @param $result
     * @param $offset
     * @return bool|object
     */
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

    /**
     * @param $c
     * @param $argc
     * @param bool $noerr
     * @return mixed|null
     */
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

    /**
     * @param $msg
     * @param $tr
     * @return string
     */
    function mysql_trigger_msg ($msg, $tr) {
        return sprintf ('%s: %s in %s on lien %d', $tr['function'], $msg, $tr['file'], $tr['line']);
    }
}
