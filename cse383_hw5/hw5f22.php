<?php

// CHANGE THIS
$TABLE="383f22";
$PASSWORD="55gf343asdf";


/*
CSE383
Fall 2022
HW5 Form Assignment

Assumes the form is sending the following fields:



. useridf22 > text
. emailf22f22 > email field
. entryYearf22 > entry year > radio button(s) (use 2018,2019,2020,2021,2022,0 (label = other))
. birthYearf22 > use select box (1960 to now)
. gradDatef22 > Expected Graduation Date  (date field)
. csef22 > Are you a CSE major> use checkbox
. carf22 > Do you own a car > checkbox
. mobileDevicef22 > Number of mobile devices (including laptops, ipads, etc) you own > select (0-10)
. quotef22 > Favorite quote > text area (5 lines by 40 columns)
. quoteCitef22 > Source of quote (site, who said it, etc) >  url
. commandf22 > Hidden field with value = "submit"
. great looking submit button
. clear button

Requirements:

. MUST use POST
. All non-checkbox fields are required - use bootstrap requirement
. Provide hints in the text fields

 */
if (count($_POST) == 0) die("NO POST DATA FOUND - STOPPING\n");
if (isset($_REQUEST["debug"])) {
	print "<pre>";
	print_r($_REQUEST);
	if (! isset($_POST["debug"])) print "<b>DATA SENT USING GET NOT POST - ERROR</b>\n";
	exit(0);
}
function getVar($name) {
	if (!isset($_REQUEST[$name])) {
		return "";
	}
	else return htmlspecialchars($_REQUEST[$name]);
}

//
// Check for valid entry of required fields
//
$FIELDS=array( "useridf22", "emailf22", "entryYearf22", "birthYearf22", "gradDatef22", "mobileDevicef22", "quotef22", "quoteCitef22", "commandf22");
$err=0;
foreach ($FIELDS as $field) {
	if (! isset($_REQUEST[$field])) {
		print "Missing Required field $field<br>";
		$err++;
	}
}
if ($err) die("Missing required fields");

//connect to DB
try {
	//connect to DB
	@$mysqli = new mysqli("localhost","383",$PASSWORD,"383");
	//
	if ($mysqli->connect_errno) {
		die("Can't connect to db -> did you update password?");
	}
	// echo 'Connected to database';
}
catch(PDOException $e)
{
	die("Can't connect to db -> did you update password?");
}

// return average age
function avgAge() {
	global $mysqli;
	global $TABLE;
	$year=date("Y");

	$result = $mysqli->query("select avg(birthYear) from $TABLE where birthYear between 1960 and $year ");
	if (!$result) {
		print ($mysqli->error);
		return "";
	} else {
		$a = $result->fetch_array();
		return $year-$a[0];
	}
}
// return average grad
function entryYear() {
	global $mysqli;
	global $TABLE;
	$year=date("Y")+10;
	$result = $mysqli->query("select avg(entryYear) from $TABLE where entryYear between 1960 and $year ");
	if (!$result) {
		print ($mysqli->error);
		return "";
	} else {
		$a = $result->fetch_array();
		return $a[0];
	}
}
// return num CSE
function cse() {
	global $mysqli;
	global $TABLE;
	$result = $mysqli->query("select count(pk) from $TABLE where cse!=''  ");
	if (!$result) {
		print ($mysqli->error);
		return "";
	} else {
		$a = $result->fetch_array();
		return $a[0];
	}
}

//return last quote
function lastQuote() {
	global $mysqli;
	global $TABLE;
	$result = $mysqli->query("select quote from $TABLE where quote is not null and quote != ''   order by pk desc");
	if (!$result) {
		print ($mysqli->error);
		return "";
	} else {
		$a = $result->fetch_array();
		return $a[0];
	}

}
// return random quote
function rndQuote() {
	global $mysqli;
	global $TABLE;
	$result = $mysqli->query("select quote,rand() as random from $TABLE where quote is not null and quote != ''   order by 2 limit 1");
	if (!$result) {
		print ($mysqli->error);
		return "";
	} else {
		$a = $result->fetch_array();
		return $a[0];
	}

}

//return sum of all devices
function numDevices() {
	global $mysqli;
	global $TABLE;
	$cmd= "select sum(mobileDevices) from $TABLE where mobileDevices between 1 and 10 ";
	$result = $mysqli->query($cmd);
	if (!$result) {
		print "$cmd<br>";
		print ($mysqli->error);
		return "";
	} else {
		$a = $result->fetch_array();
		return $a[0];
	}

}


//crate default values

$cmd = getVar("commandf22");
$uid="";
$entryYear = "";
$birthYear = "";
$cse="";
$car="";
$mobileDevices="";
$quote="";
$quoteCite="";

//if form submittal submit
if ($cmd=="submit") {
	$uid = getVar("useridf22");
	$entryYear = getVar("entryYearf22");
	$birthYear = getVar("birthYearf22");
	$cse = getVar("csef22");
	$car = getVar("carf22");
	$mobileDevices = getVar("mobileDevicef22");
	if (!is_numeric($mobileDevices))
		$mobileDevices = -1;
	$quote = getVar("quotef22");
	$quoteCite = getVar("quoteCitef22");

	$stmt = $mysqli->prepare("insert into $TABLE" . "(uid,entryYear,birthYear,cse,car,mobileDevices,quote,quoteCite) values (?,?,?,?,?,?,?,?)");
	if (!$stmt) {
		die("error on prepare statement err='" . $mysqli->error . "'");
	}
	if (!$stmt->bind_param("siississ",$uid,$entryYear,$birthYear,$cse,$car,$mobileDevices,$quote,$quoteCite)) {
		print "Error on bind";
	} else {
		if (!$stmt->execute()) {
			print "Error on execute";
			print $mysqli->error;
			exit(0);
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Johnsok9 HW5 F22 Project</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>
/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
.row.content {height: 550px}

/* Set gray background color and 100% height */
.sidenav {
	background-color: #f1f1f1;
height: 100%;
}

/* On small screens, set height to 'auto' for the grid */
@media screen and (max-width: 767px) {
	.row.content {height: auto;}
}
</style>
</head>
<body>

<nav class="navbar navbar-inverse visible-xs">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="#">Logo</a>
</div>
<div class="collapse navbar-collapse" id="myNavbar">
<ul class="nav navbar-nav">
<li class="active"><a href="#">Display Results</a></li>
<li><a href="index.html">List</a></li>
<li><a href="http://miamioh.edu">Miami</a></li>
</ul>
</div>
</div>
</nav>

<div class="container-fluid">
<div class="row content">
<div class="col-sm-3 sidenav hidden-xs">
<h2>Logo</h2>
<ul class="nav nav-pills nav-stacked">
<li class="active"><a href="hw5.php">Redisplay Results</a></li>
<li><a href="index.html">Back to form</a></li>
</ul><br>
</div>
<br>

<div class="col-sm-9">
<div class="well">
<h4>You Submitted</h4>
<div>Uid=<?php print $uid;?></div>
<div>Entry Year=<?php print $entryYear;?></div>
<div>BirthYear=<?php print $birthYear;?></div>
<div>CSE=<?php print $cse;?></div>
<div>Car=<?php print $car;?></div>
<div>Number of mobile devices=<?php print $mobileDevices;?></div>
<div>Quote = <?php print $quote;?></div>
<div>Quote Source = <?php print $quoteCite;?></div>

</div>
<div class="row">
<h2>Average Answers from all users</h2>
<div class="col-sm-3">
<div class="well">
<h4>Total Mobile Devices</h4>
<p><?php  print numDevices();?></p>
</div>
</div>
<div class="col-sm-3">
<div class="well">
<h4>Average Age</h4>
<p><?php  print avgAge();?></p>
</div>
</div>
<div class="col-sm-3">
<div class="well">
<h4>NumCSE</h4>
<p><?php  print cse();?></p>
</div>
</div>
<div class="col-sm-3">
<div class="well">
<h4>entryYear</h4>
<p><?php  print entryYear();?></p>
</div>
</div>
</div>
<div class="row">
<div class="col-sm-4">
<div class="well">
<p>Last Quote Entered by Anyone: <?php  print lastQuote();?></p>
</div>
</div>
<div class="col-sm-4">
<div class="well">
<p>Random Quote: <?php  print rndQuote();?></p>
</div>
</div>
<div class="col-sm-4">
<div class="well">
<p>Another Random Quote: <?php  print rndQuote();?></p>
</div>
</div>
</div>
</div>
</div>
</div>

</body>
</html>

