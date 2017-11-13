<?
function sql_trigger_error($q, $query, $err)
{
    global $debug;
    if ($debug) {
        $h = "<br>";
        $h .= "<b>Query Function:</b>";
        $h .= "<ul><i>" . $query . "</i></ul>";
        $h .= "<b>Query Postgres:</b>";
        $h .= "<ul><i>" . $q . "</i></ul>";
        $h .= "<b>Error Details:</b>";
        $h .= "<ul><i>$err</i></ul>";

        trigger_error($h);
    } else {
        echo "<b>SQL Error.</b>";
    }
    die();
}

function sql_query($query, $database = 0)
{
    global $debug, $db;
    if ($database == 0) {
        $database = &$db;
    }
    $q = preg_replace("'UNIX_TIMESTAMP\('si", "date_part('epoch',", $query);
    $q = preg_replace("'FROM_UNIXTIME'si", "abstime", $q);
    $q = preg_replace("'DAYOFMONTH\('si", "date_part('day',", $q);
    $q = preg_replace("'MONTH\('si", "date_part('month',", $q);
    $q = str_replace("as SIGNED", "as INT", $q);
    $q = preg_replace("/LIMIT (\d{1,}),(\d{1,})/si", "LIMIT $2 OFFSET $1", $q);
    $q = preg_replace("/(\W{1,})(begin)(\W{1,})/", "$1\"begin\"$3", $q);
    $q = preg_replace("/(\W{1,})(end)(\W{1,})/", "$1\"end\"$3", $q);
    $q = preg_replace("/LIKE/si", "ILIKE", $q);
    if (strpos($q, "INSERT") === false) {
        //do nothing
    } else {
        // zoek de SET
        $begin = strpos($q, "SET");
        if ($begin) {
            $q = str_replace(";", "", $q);
            $deel = substr($q, $begin + 4, strlen($q) - 1);
            $deel = explode(",", $deel);
            $velden = "";
            $inhoud = "";
            foreach ($deel as $val) {
                $veld = substr($val, 0, strpos($val, "="));
                $waarde = rtrim(ltrim(substr($val, strpos($val, "=") + 1, strlen($val))));
                if ($velden) {
                    $velden .= ", $veld";
                    $inhoud .= ", " . str_replace("\"", "'", $waarde);
                } else {
                    $velden = $veld;
                    $inhoud = str_replace("\"", "'", $waarde);
                }
            }
            $q = substr($q, 0, $begin);
            $q .= " ($velden) VALUES ($inhoud)";
        }
    }
    $result = @pg_query($q);
    if (pg_last_error($db)) {
        sql_trigger_error($query, $q, pg_last_error($db));
    }
    return $result;
}

function sql_fetch_array($result)
{
    return pg_fetch_array($result);
}

function sql_fetch_row($result)
{
    return pg_fetch_row($result);
}

function sql_fetch_assoc($result)
{
    return pg_fetch_assoc($result);
}

function sql_result($result, $pos = 0, $field = "")
{
    if ($field) {
        $ret = pg_fetch_array($result, $pos);
        $return = $ret[$field];
    } else {
        $ret = pg_fetch_array($result, $pos);
        $return = $ret[0];
    }
    return $return;
}

function sql_num_rows($result = "")
{
    return pg_num_rows($result);
}

function sql_affected_rows($result = "")
{
    return pg_affected_rows($result);
}

function sql_data_seek($result, $pos)
{
    return pg_result_seek($result, $pos);
}

function sql_insert_id($table)
{
    $q = "SELECT currval('" . $table . "_id_seq')";
    $res = pg_query($q);
    $row = pg_fetch_array($res, 0);
    $return = $row[0];
    return $return;
}

function sql_error($filename = "unknown", $linenumber = 0, $query = "")
{
    global $debug, $db;
    return 1;
}

?>
