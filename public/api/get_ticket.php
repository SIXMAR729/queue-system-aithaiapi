<?php
//  File: /public/api/get_ticket.php
//
//  This is a public "entry point".
//  Its only job is to load the real logic file from the private 'src' folder.
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../../src/get_ticket.php';

?>