<?php

use Http as Http;
use Http\HttpHeader as HttpHeader; 
use Http\AcceptHeader as AcceptHeader; 
use Http\HttpResponse as HttpResponse;
use Http\HttpRequest as HttpRequest;
use Http\HttpHeaderCollection;
use Salesforce\OAuth;
use Salesforce\OAuthException;
use \Salesforce\RestApiRequest;
use Ocdla\Template;


class SpecialCle extends SpecialPage {
	
    public function __construct() {


        parent::__construct("Cle");

		$this->getOutput()->addModules("ext.cle");

		$this->mIncludable = true;	
    }


	private static function authenticate() {
		global $oauth_config;
		$config = new Salesforce\OAuthConfig($oauth_config);

        $flow = "usernamepassword";

        $httpMessage = OAuth::start($config, $flow);

        $resp = $httpMessage->authorize();

        if(!$resp->isSuccess()) throw new OAuthException($resp->getErrorMessage());

        $url = $resp->getInstanceUrl();
        $token = $resp->getAccessToken();
        // var_dump($url, $token );exit;
        // CoreModule::setSession($config->getName(), $flow, $oauthResp->getInstanceUrl(), $oauthResp->getAccessToken());
        cache_set("instance_url", $url);
        cache_set("access_token", $token); 

		// Application::writeCredentialsToCache($config);
		return array($url,$token);
	}
	

	
	// select Event__r.Start_Date__c, Title__c, Event__r.Name from Chapter__c where Title__c LIKE '%jury%' 
	private static function queryProducts($year) {


		// Subscription should last only a year, but we dont have a reliable way of determining expiration.
		//$query = "SELECT Id FROM OrderItem WHERE Contact__c = '$contactId' AND RealExpirationDate__c > $today AND Product2id IN($soqlProdIds)";
		$query = "SELECT Id, Name, Start_Date__c FROM Event__c WHERE CALENDAR_YEAR(Start_Date__c)={$year} ORDER BY Start_Date__c DESC";
		// $query = "SELECT Event__r.Start_Date__c, Title__c, Event__r.Name FROM Chapter__c where Title__c LIKE '%jury%'";


		$token = cache_get("access_token");
		$url = cache_get("instance_url");

		if(empty($token) || empty($url)) {
			list($url,$token) = self::authenticate();
		}


		try {
			// If the access token has been removed from the session, return false...for now.  (Need a better solution)
		
			$api = new RestApiRequest($url, $token);
			$resp = $api->query($query);

			if(!$resp->success()) {
				throw new \Exception("foobar");
			}
		} catch(\Exception $e) {

			if(function_exists("opcache_invalidate")) {
				$result = opcache_invalidate(CACHE_DIR . "/access_token", true);
				$result = opcache_invalidate(CACHE_DIR . "/instance_url", true);
			}

			cache_delete("access_token");
			cache_delete("instance_url");

			// return $this->runHttp($req);
			return self::queryProducts($year);
		}


		// var_dump($resp);exit;
		// if(!$resp->success()) throw new \Exception($resp->getErrorMessage());

		return $resp->success() && count($resp->getRecords()) > 0 ? $resp->getRecords() : array();
	}


	public function execute($params) {

		// var_dump($params); exit;

		// cache_set("foobar","baz");
		
		$output = $this->getOutput();

		try {
			$records = self::queryProducts(2021);
			$rows = array_map(function($elem) { 
				$id = $elem["Id"];
				$date = $elem["Start_Date__c"];
				$titleDate = new DateTime($date);
				$titleDate = $titleDate->format("F jS, Y");
				return "<a href='/CLE/{$id}'>" . $elem["Name"] . "</a> - {$titleDate}";
			}, $records);
			$html = "<ul><li>" . implode("</li><li>", $rows) . "</ul></li>";
		} catch(\Exception $e) {
			$html = $e->getMessage();
		}

		$output->addHTML($html);
	}


    public function preExecute($params) {

		global $wgOcdlaCaseReviewsDefaultRecordLimit;

		// Set some default parameters.
		$params = empty($params) ? "50" : $params;

		list($numRows, $field, $value) = explode("/", $params);
		$field = "subject_1" == $field ? "subject" : $field;
		
		

		$output = $this->getOutput();

		$template = __DIR__ . "/templates/summary.tpl.php";

		// Use when determining whether.
		// if(!$this->including())

		

		$html = $useAlternateTemplate ? $this->getAlternateHTML($cars, $template) : $this->getHTML($days, $template);

		$output->addHTML($html);
    }






	
	public function group($cars){
		
		$days = array();

		// Assumes results are already sorted DESC by year, month, and day, so array will start with most recent cars.
		foreach($cars as $car){

			// Normally we can accept 2021-11-04 but could we try 2021-11-4 (our database doesn't store leading zeros)?
			$key = implode("-", array($car["year"], $car["month"], $car["day"], $car["court"]));

			$days[$key][] = $car;
		}

		return $days;
	}


	public function getHTML($days, $summaryTemplate) {

		$subjectTemplate = __DIR__ . "/templates/subjects.tpl.php";

		// If the page is being rendered as a standalone page, add the additional html.
		$html = !$this->including() ? $this->getSummaryLinksHTML() : "";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($days as $key => $cars){

			$params["cars"] = $cars;

			$params = $this->preprocess($key, $cars);

			$params["subjectsHTML"] = Template::renderTemplate($subjectTemplate, $params);

			$html .= Template::renderTemplate($summaryTemplate, $params);
		}

		// Closing container tags
		$html .= "</div></div>";

		return str_replace(array("\r", "\n"), '', $html);
	}


	public function getAlternateHTML($cars, $template) {

		global $wgOcdlaAppDomain;

		$subject = $cars[0]["subject"];

		$html = "<h5>Showing " . ucwords($cars[0]["subject"]) . " Case Reviews</h5>";
		$html .= "<a href='$wgOcdlaAppDomain/car/list?subject=$subject'>Show all $subject case reviews</a>";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($cars as $car){

			$year = $car["year"];
			$month = $car["month"];
			$day = $car["day"];

			$titleDate = new DateTime("$year-$month-$day");
			$titleDate = $titleDate->format("F jS, Y");

			$car["titleDate"] = $titleDate;
			$car["appDomain"] = $wgOcdlaAppDomain;

			$html .= Template::renderTemplate($template, $car);
		}

		// Closing container tags
		$html .= "</div></div>";

		return str_replace(array("\r", "\n"), '', $html);
	}



	public function preprocess($key, $cars){

		global $wgOcdlaAppDomain, $wgOcdlaCaseReviewAuthor;

		list($year, $month, $day, $court) = explode("-", $key);
		

		// Doing this for the title only.  Don't want to change the value of "$court".
		$titleCourt = !empty($court) ? $court : "Case Reviews";
		$titleDate = new DateTime("$year-$month-$day");
		$titleDate = $titleDate->format("F jS, Y");

		$title = "$titleCourt, $titleDate";

		// Build the published date, but only if the create time is a valid timestamp.
		if($this->timestampIsValid($cars[0]["published_date"])){

			$publishDate = $cars[0]["published_date"];
			$publishDate = new DateTime($publishDate);
			$publishDate = $publishDate->format("F jS, Y");
		}




		$data = array(
			"title"		   => $title,
			"titleDate"    => $titleDate,
			"publishDate"  => $publishDate,
			"author"	   => $wgOcdlaCaseReviewAuthor,
			"year"		   => $year,
			"month"		   => $month,
			"day"		   => $day,
			"court"		   => $court,
			"appDomain"	   => $wgOcdlaAppDomain,
			"cars"		   => $cars
		);

		return $data;
	}


	public function getSummaryLinksHTML(){

		global $wgServer, $wgOcdlaAppDomain;

		$template = __DIR__ . "/templates/summary-links.tpl.php";

		$years = DbHelper::getDistinctFieldValues("car", "year");


		// These are the links to case reviews that are not available in the app.
		$legacyLinks = array(
			"2015"	=>  "$wgServer/2015_Case_Summaries_by_Topic",
			"2016"	=>  "$wgServer/2016_Case_Summaries_by_Topic",
			"2017"	=>  "$wgServer/2017_Case_Summaries_by_Topic"

		);


		// These are the links to summaries in the app.
		$appLinks = array();

		foreach($years as $year) {
			
			$appLinks[$year] = "$wgOcdlaAppDomain/car/list?year=$year&summarize=1";
		}

		$allSummaryLinks = $legacyLinks + $appLinks;

		return Template::renderTemplate($template, array("allSummaryLinks" => array_reverse($allSummaryLinks, true)));
	}


	public function timestampIsValid($timestamp){

		$year = (int) explode("-", $timestamp)[0];

		return $year > 2000;
	}
}
