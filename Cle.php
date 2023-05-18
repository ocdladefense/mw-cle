<?php

if (!defined("MEDIAWIKI")) die();

// White list the special page, so it is public.
$wgWhitelistRead[] = "Special:Cle";


# Autoload classes and files
$wgAutoloadClasses["SpecialCle"] = __DIR__ . "/SpecialCle.php";


# Tell MediaWiki about the new special page and its class name
$wgSpecialPages["Cle"] = "SpecialCle";


$wgResourceModules["ext.cle"] = array(
    "scripts" => array(),
    "styles" => array(
        "css/cle.css"
    ),
    "position" => "bottom",
    "remoteBasePath" => "/extensions/Cle",
    "localBasePath" => "extensions/Cle"
);