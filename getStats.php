<?php
include("config.php");


$_GET["id"] = $config["ids"];
if(empty($_GET)) {
	print "ERROR: Wrong usage";
	die();
}

if(!isset($_GET["appcode"])) {
	print "ERROR: Wrong appcode";
	die();
}

if($_GET["appcode"] != $config["appcode"]) {
	print "ERROR: Wrong appcode";
	die();
}

if(!isset($_GET["id"]) && !isset($_GET["name"])) {
	print "ERROR: Wrong usage";
	die();
}

include("UbiAPI.php");

$uapi = new UbiAPI($config["ubi-email"],$config["ubi-password"]);

$data = array();
$stats = $config["default-stats"];
$season = -1;

if(isset($_GET['season'])) {
	$season = $_GET['season'];
}

$platform = $config["default-platform"];
if(isset($_GET['platform'])) {
	$platform = $_GET['platform'];
}

if(isset($_GET['stats'])) {
	$stats = $_GET['stats'];
}

$notFound = [];

function printName($uid)
{
	global $uapi, $data, $id, $platform, $notFound;
	$su = $uapi->searchUser("byid", $uid, $platform);
	if ($su["error"] != true) {
		$data[$su['uid']] = array(
			"profile_id" => $su['uid'],
			"nickname" => $su['nick']
		);
	} else {
		$notFound[$uid] = [
			"profile_id" => $uid,
			"error" => [
				"message" => "User not found!"
			]
		];
	}
}

function printID($name)
{
	global $uapi, $data, $id, $platform, $notFound;
	$su = $uapi->searchUser("bynick", $name, $platform);
	if ($su["error"] != true) {
		$data[$su['uid']] = array(
			"profile_id" => $su['uid'],
			"nickname" => $su['nick']
		);
	} else {
		$notFound[$name] = [
			"nickname" => $name,
			"error" => [
				"message" => "User not found!"
			]
		];
	}
}

if(isset($_GET["id"])) {
	$str = $_GET["id"];
	if (strpos($str, ',') !== false) {
		$tocheck = explode(',', $str);
	}else{
		$tocheck = array($str);
	}

	foreach ($tocheck as $value) {
		printName($value);
	}
}
if(isset($_GET["name"])) {
	$str = $_GET["name"];
	if (strpos($str, ',') !== false) {
		$tocheck = explode(',', $str);
	}else{
		$tocheck = array($str);
	}

	foreach ($tocheck as $value) {
		printID($value);
	}
}

if(empty($data)) {
		$error = $uapi->getErrorMessage();
		if($error === false) {
			die(json_encode(array("players" => $notFound)));
		}else{
			die(json_encode(array("players" => array(), "error" => $error)));
		}
}

$ids = "";
foreach ($data as $value) {
	$ids = $ids . "," . $value["profile_id"];
}
$ids = substr($ids, 1);
$ids = $config["ids"];
$idresponse = json_decode($uapi->getStats($ids, $stats, $platform), true);
$final = array();
foreach($idresponse["results"] as $value) {
	$id = array_search ($value, $idresponse["results"]);
	$final[$id] = array_merge($value, array("nickname"=>$data[$id]["nickname"], "profile_id" => $id, "platform" => $platform));
}
$datarespon =  str_replace(":infinite", "", json_encode(array("players" => array_merge($final,$notFound))));

$usuarios = json_decode($datarespon,true);
$usertotal = $usuarios["players"];


$usrerarray = explode(',', $config["ids"]);


?>
<!DOCTYPE html>
<html>
<head>
	<title>Prueba R6</title>
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" href="fonts/stylesheet.css" type="text/css" charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="css/custom.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
	<div class="container-fluid">
	  <h2>Striped Rows</h2>
	  <p>The .table-striped class adds zebra-stripes to a table:</p>            
	  <table class="table table-striped">
	    <thead>
	      <tr>
	      	<?php
	      		echo "<th></th>";
	      		foreach ($usrerarray as $key) {
	      			echo "<th>".$usertotal[$key]["nickname"]."</th>";
	      		}
	      	?>
	      </tr>
	    </thead>
	    <tbody>
	      <tr>
	      	<td></td>
	        <td colspan='7' class="casual">Casual</td>
	      </tr>
	      <tr>
	      	<td>casualpvp_matchwon</td>
	      	<?php

	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["casualpvp_matchwon"])) {
	      				echo "<th>".$usertotal[$key]["casualpvp_matchwon"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>casualpvp_matchlost</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["casualpvp_matchlost"])) {
	      				echo "<th>".$usertotal[$key]["casualpvp_matchlost"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>casualpvp_matchplayed</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["casualpvp_matchplayed"])) {
	      				echo "<th>".$usertotal[$key]["casualpvp_matchplayed"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>casualpvp_death</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["casualpvp_death"])) {
	      				echo "<th>".$usertotal[$key]["casualpvp_death"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>casualpvp_kills</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["casualpvp_kills"])) {
	      				echo "<th>".$usertotal[$key]["casualpvp_kills"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>casualpvp_timeplayed</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["casualpvp_timeplayed"])) {
	      				echo "<th>".$usertotal[$key]["casualpvp_timeplayed"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td></td>
	        <td  colspan='7'>Ranked</td>
	      </tr>
	      <tr>
	      	<td>rankedpvp_matchwon</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["rankedpvp_matchwon"])) {
	      				echo "<th>".$usertotal[$key]["rankedpvp_matchwon"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>rankedpvp_matchlost</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["rankedpvp_matchlost"])) {
	      				echo "<th>".$usertotal[$key]["rankedpvp_matchlost"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>rankedpvp_matchplayed</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["rankedpvp_matchplayed"])) {
	      				echo "<th>".$usertotal[$key]["rankedpvp_matchplayed"]."</th>";
					}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>rankedpvp_death</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["rankedpvp_death"])) {
	      				echo "<th>".$usertotal[$key]["rankedpvp_death"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>rankedpvp_kills</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["rankedpvp_kills"])) {
	      				echo "<th>".$usertotal[$key]["rankedpvp_kills"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	      <tr>
	      	<td>rankedpvp_timeplayed</td>
	      	<?php
	      		foreach ($usrerarray as $key) {
	      			if (isset($usertotal[$key]["rankedpvp_timeplayed"])) {
	      				echo "<th>".$usertotal[$key]["rankedpvp_timeplayed"]."</th>";
	      			}
	      			else
	      			{
	      				echo "<th>N/A</th>";
	      			}
	      		}
	      	?>
	      </tr>
	    </tbody>
	  </table>
	</div>



	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>