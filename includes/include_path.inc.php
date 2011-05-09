<?php
$dsep = DIRECTORY_SEPARATOR;
$IP = dirname( realpath( dirname( dirname ( __FILE__) . "$dsep..$dsep") ) );
$sep = (DIRECTORY_SEPARATOR == "\\") ? ";" : ":";
ini_set( "include_path", ini_get( "include_path" ). "$sep$IP$sep" );// Add current directory
ini_set( "include_path", ini_get( "include_path" ).$IP.$dsep."classes". "$sep" ); // Add classes directory
ini_set( "include_path", ini_get( "include_path" ). $IP.$dsep."classes".$dsep."security" ); // Add classes/security directory
