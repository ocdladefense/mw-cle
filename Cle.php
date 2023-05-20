<?php
/*
Subject, Year (most recent at top of list)



CLE object="chapter" groupBy="seminar" orderBy="desc" orderByField="seminar_date limit="10"


Grouped by  AC, DUII, S/S, Juvenile Law,, then by "special topics"


More attorneys use the LOD then using the CLE store pages; there's benefit to displaying the CLEs in LOD.



Chapter list in AMS could be queries.



// List CLE talks (regardless of seminar) that contain the keyword in the Chapter title.
{CLE keyword="jury" orderBy="desc" orderByField="seminar_date limit="10"}

Item 1
Item 2
Item 3 (xxx)

URL for listing CLEs by Year
Level 1 (top level) - https://lodtest.ocdla.org/CLEs

Level 2 - https://lodtest.ocdla.org/CLEs/AJLC-2023


URL for listing CLEs by Subject
 https://lodtest.ocdla.org/CLEs/subject/jury+instruction
https://lodtest.ocdla.org/CLEs/subject/jury+selection




Info query
select Event__r.Start_Date__c, Title__c, Event__r.Name from Chapter__c where Title__c LIKE '%jury%' 

Product query
select Event__r.Start_Date__c, Title__c, Event__r.Name from Chapter__c where Title__c LIKE '%jury%' 


SELECT FROM Product2 WHERE Family='CLE' AND Event__c = 'the-event-id-we're interested in'
*/

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