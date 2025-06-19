<?php

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'legacyvsx';
// config variables for mysql db

function connectDB()
{
        global $dbhost,$dbuser,$dbpass,$dbname;
        $conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname) or die('Error connecting to mysql');
        //mysql_select_db($dbname);

        mysqli_set_charset($conn,'utf8');
        return $conn;
}

function disconnectDB($link)
{
        mysqli_close($link);
}

function executeQuery($sql,$link)
{
        //$sql    = 'SELECT foo FROM bar WHERE id = 42';
        $result = mysqli_query($link,$sql);

        if (!$result) {
                echo "Query: {$sql}<BR>";
                echo "DB Error, could not query the database\n";
                echo 'MySQL Error: ' . mysqli_error($link);
                exit;
        }
        else
                return $result;
}

?>
