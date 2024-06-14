<?php
$server="localhost";
$dbName="blood_db";
$user="root";
$password="wilber";
$connect=  mysql_connect($server, $password, $user);
$selectDB=  mysql_select_db($dbName,$connect);

//for storing details to the database
/*
 * pick content from the form
 */
$contact=$_POST['contact'];
$birth_date=$_POST['birthday'];
$name=$_GET['name'];
/*
 * code for inserting into the database
 * 
 */
$insertQuery=  mysql_query("Insert into contact (Contact,Birth_Date,Name) values ('$contact','$birth_date','$name')")
        
?>