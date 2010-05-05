<?php

function escape_html($str) {
    return htmlentities($str, ENT_QUOTES, 'ISO-8859-1', false); // don't double encode
}
