<?php

/**
 * Mysql function lib for PureFTPd.
 *
 * This file holds all the mysql funtions used in the class file
 * @version 0.2.0
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://pureuseradmin.sourceforge.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2004, Michiel van Baak
 */

/**
 * Error handler.
 * @param string $query The query sent to the database server.
 * @param string $err User supplied extra error info.
 * @return string Error page with backtrace.
 * @access private
 */
function sql_trigger_error($query, $err)
{
    if (pureuseradmin::DEBUG) {
        $h = "<br>";
        $h .= "<b>Query Mysql/Function:</b>";
        $h .= "<ul><i>" . $query . "</i></ul>";
        $h .= "<b>Error Details:</b>";
        $h .= "<ul><i>$err</i></ul>";
        $h .= "<b>Debug Trace:</b>";
        $h .= "<ul>";
        $vDebug = debug_backtrace();
        $h .= "<table border=\"0\" cellcpacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\"><tr>";
        $h .= "<td bgcolor=\"#CDCDCD\">Function Name</td>";
        $h .= "<td bgcolor=\"#CDCDCD\">File Name</td>";
        $h .= "<td bgcolor=\"#CDCDCD\">Line</td>";
        $h .= "</tr>";
        for ($i = 1; $i < count($vDebug); $i++) {
            $val = $vDebug[$i];
            if ($i == 1) {
                $bg = "#EC7C7C";
            } else {
                $bg = "#FFFFFF";
            }
            $h .= "<tr>";
            $h .= "<td bgcolor=\"$bg\">" . $val["function"] . "</td>";
            $h .= "<td bgcolor=\"$bg\">" . $val["file"] . "</td>";
            $h .= "<td bgcolor=\"$bg\">" . $val["line"] . "</td>";
            $h .= "</tr>";
        }
        $h .= "</table>";
        $h .= "</ul>";
        print($h);
    } else {
        echo "<b>SQL Error.</b>";
    }
    die();
}

/**
 * Send query to database server.
 * @param string $query The query sent to the database server.
 * @return resource Mysql result resource.
 * @access public
 */
function sql_query($query)
{
    $result = mysql_query($query) or sql_trigger_error($query, mysql_error());
    return $result;
}

/**
 * Fetch a result row as an associative array, a numeric array, or both.
 * @param resource $result The result resource from a previous sql_query call.
 * @return array Returns an array that corresponds to the fetched row, or FALSE  if there are no more rows.
 * @access public
 */
function sql_fetch_array($result)
{
    return mysql_fetch_array($result);
}

/**
 * Get a result row as an enumerated array.
 * @param resource $result The result resource from a previous sql_query call.
 * @return array Returns an array that corresponds to the fetched row, or FALSE  if there are no more rows.
 * @access public
 */
function sql_fetch_row($result)
{
    return mysql_fetch_row($result);
}

/**
 * Fetch a result row as an associative array.
 * @param resource $result The result resource from a previous sql_query call.
 * @return array Returns an associative array that corresponds to the fetched row, or FALSE if there are no more rows.
 * @access public
 */
function sql_fetch_assoc($result)
{
    return mysql_fetch_assoc($result);
}

/**
 * Get result data.
 * @param resource $result The result resource from a previous sql_query call.
 * @param integer $pos The offset - optional.
 * @param string $field The field in the result array - optional.
 * @return mixed The contents of one cell from a MySQL result set.
 * @access public
 */
function sql_result($result, $pos = 0, $field = "")
{
    if ($field) {
        $return = mysql_result($result, $pos, $field);
    } else {
        $return = mysql_result($result, $pos);
    }
    return $return;
}

/**
 * Get number of rows in result.
 * @param resource $result The result resource from a previous sql_query call - optional.
 * @return integer The number of rows in a result set.
 * @access public
 */
function sql_num_rows($result = "")
{
    return mysql_num_rows($result);
}

/**
 * Get number of affected rows in previous MySQL operation.
 * @param resource $result The result resource from a previous sql_query call - optional.
 * @return integer The number of rows affected by the last INSERT, UPDATE or DELETE query.
 * @access public
 */
function sql_affected_rows($result = "")
{
    return mysql_affected_rows();
}

/**
 * Move internal result pointer.
 * @param resource $result The result resource from a previous sql_query call.
 * @param integer Position in data set
 * @return boolean TRUE on success or FALSE on failure.
 * @access public
 */
function sql_data_seek($result, $pos)
{
    return mysql_data_seek($result, $pos);
}

/**
 * Get the ID generated from the previous INSERT operation.
 * @return integer The ID generated for an AUTO_INCREMENT column by the previous INSERT query.
 * @access public
 */
function sql_insert_id()
{
    return mysql_insert_id();
}

/**
 * Obsolete error handler. It's still here for backward compatability
 */
function sql_error($filename, $linenumber, $query = "")
{
    return 1;
}

?>
