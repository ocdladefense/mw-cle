<?php


use Ocdla\Template;

require "src/Salesforce.php";

class SpecialCle extends SpecialPage {
	
    public function __construct() {


        parent::__construct("Cle");

		$this->getOutput()->addModules("ext.cle");

		$this->mIncludable = true;	
    }





	public function execute($params) {

		$html = (true || $this->including()) ? $this->shortcode($params) : $this->standalone($params);
		
		$output = $this->getOutput();

		$output->addHTML($html);
	}




	public function shortcode($params) {

		// SELECT Event__r.Start_Date__c, Title__c, Event__r.Name from Chapter__c where Title__c LIKE '%jury%' 
		// Subscription should last only a year, but we dont have a reliable way of determining expiration.
		// $query = "SELECT Id FROM OrderItem WHERE Contact__c = '$contactId' AND RealExpirationDate__c > $today AND Product2id IN($soqlProdIds)";
		$query = "SELECT Id, Name, Start_Date__c FROM Event__c WHERE CALENDAR_YEAR(Start_Date__c)={$params} ORDER BY Start_Date__c DESC";
		// $query = "SELECT Event__r.Start_Date__c, Title__c, Event__r.Name FROM Chapter__c where Title__c LIKE '%jury%'";
	

		try {
			
			$records = Salesforce::doQuery($query);
			$tpl = new Template(__DIR__ . "/templates/seminars");
			$html = $tpl->render(array("seminars" => $records));

		} catch(\Exception $e) {
			$html = $e->getMessage();
		}

		return $html;
	}




	public function standalone($params) {

		$query = "SELECT Id, Name, Start_Date__c FROM Event__c WHERE Id = '%s'";
		$query = sprintf($query,$params);	


		$queryc = "SELECT Id, Name, Title__c FROM Chapter__c WHERE Event__c = '%s'";
		$queryc = sprintf($queryc,$params);

		$event = self::doQuery($query);
		$chapters = self::doQuery($queryc);

		// var_dump($chapters);exit;


		$tpl = new Template(__DIR__ . "/templates/chapters");
		$html = $tpl->render(array(
			"event" => $event,
			"chapters" => $chapters
		));

		return $html;
	}




	

}
