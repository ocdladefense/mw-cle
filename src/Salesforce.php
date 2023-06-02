<?php
use Http as Http;
use Http\HttpHeader as HttpHeader; 
use Http\AcceptHeader as AcceptHeader; 
use Http\HttpResponse as HttpResponse;
use Http\HttpRequest as HttpRequest;
use Http\HttpHeaderCollection;
use Salesforce\OAuth;
use Salesforce\OAuthException;
use Salesforce\RestApiRequest;



class Salesforce {


	public static function doQuery($query) {

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
			return self::doQuery($year);
		}


		// var_dump($resp);exit;
		// if(!$resp->success()) throw new \Exception($resp->getErrorMessage());

		return $resp->success() && count($resp->getRecords()) > 0 ? $resp->getRecords() : array();
	}




	public static function authenticate() {
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
	

}