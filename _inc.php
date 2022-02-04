<?php $dir="db/"; // Edit this lol







session_start(); include "kekdown.php";
$config = json_decode(file_get_contents("db/config.json"));
date_default_timezone_set($config->{"timezone"});
$title = htmlentities($config->{"title"});
$description = htmlentities($config->{"description"});
$utterances = $config->{"utterances"};
$copy = $config->{"footer"};


/* Mini-includes */
$style="@import url('https://fonts.googleapis.com/css2?family=Anonymous+Pro:ital,wght@0,400;0,700;1,400;1,700&display=swap');body{padding:3px 10%;font-family:'Anonymous Pro', monospace;background:#cfcfcf;}p{font-size:normal;}#home a#logo{font-size:xx-large;font-weight:bold;}a#logo{color:black;text-decoration:none;}.notlogo{margin:25px 0px;}#vote{margin:17px 0;}#search input[type=text]{width:99.5%;padding:0.2%;}#search input[type=submit]{display:none;}#incsrch input[type=text]{font-family:'Anonymous Pro', monospace;width:50%;font-weight:bold;font-style:italic;}footer{margin:1em 0 0 0;}";
$footer="</div><hr id='prf'><footer>".htmlentities($copy)."<span style='float:right'><a style='color:black' href='index.php?liteldom'>Pwd by Liteldom</a></span></footer></body></html>";
$title = htmlentities($title);
$custom_css = filesize("custom.css");
if($custom_css != 0){
  $custom_css = '<link rel="stylesheet" href="custom.css">';
}else{
  $custom_css = '';
}
$h0="<!DOCTYPE html><html lang='".$config->{"lang"}."'><head><meta name='viewport' content='width=device-width,initial-scale=1'><title>";
$h1=" | $title</title><style>$style</style>$custom_css</head><body><div><a id='logo' style='text-align:left' href='index.php?'><b><= $title</b></a>"; ?>