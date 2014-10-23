<?php
/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

use Isuttell\LaravelAnalytics\VisitorAnalytics as VisitorAnalytics;
use Isuttell\LaravelAnalytics\PageView as PageView;

Route::filter('analytics', function()
{
	try {
		/*--------------------------------------------------------------------------
		| Record Visitor Information
		*/

		$analyticsSession = null;


		// Check to see if we already have data for this user
		if(Session::has('visitoranalytics_id')) $analyticsSession = VisitorAnalytics::find(Session::get('visitoranalytics_id'));


		// If not create a new session
		if(is_null($analyticsSession)) $analyticsSession = new VisitorAnalytics;

		// Save the users IP and User Agent
		$analyticsSession->ip         = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$analyticsSession->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$analyticsSession->lang       = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';


		// If the user is logged in store their id
		if(Auth::check() && $analyticsSession->user_id === 0)
		{
			$analyticsSession->user_id = Auth::user()->id;
		}


		// Keep track of the last activity so we can see how long a user was on the site
		$analyticsSession->last_activity = \Carbon\Carbon::now();


		// If browscap is set up, save browser information
		if(ini_get("browscap")) {
			$browser = get_browser(null, true);
			$analyticsSession->platform        = $browser['platform'];
			$analyticsSession->browser         = $browser['browser'];
			$analyticsSession->browser_version = $browser['version'];
			$analyticsSession->crawler         = $browser['crawler'];
			$analyticsSession->ismobiledevice  = $browser['ismobiledevice'];
			$analyticsSession->cssversion      = $browser['cssversion'];
		}

		// If GeoIP module is loaded look up location
		if(extension_loaded ('geoip'))
		{
			// Catch any errors when running geoip
			try {
				$geoIpRecord = geoip_record_by_name($_SERVER['REMOTE_ADDR']);

				//if we found a record parse it
				if($geoIpRecord !== false)
				{
					$analyticsSession->location = '';
					if(strlen( $geoIpRecord['city'] ) > 0 && strlen( $geoIpRecord['region'] ) > 0)
					{
						$analyticsSession->location = $geoIpRecord['city'] . ', ' . $geoIpRecord['region'];
					}


					//Create a GeoJSON compatibl object to store coords
					$geoJson = new stdClass();
					$geoJson->type = "point";
					$geoJson->coordinates = array(
							$geoIpRecord['latitude'],
							$geoIpRecord['longitude']
						);

					$analyticsSession->geo = json_encode($geoJson);
				}
			} catch (Exception $e) {
			    Log::error($e);
			}
		}

		// Save the data and store the id for access on the next page load
		$analyticsSession->save();
		Session::put('visitoranalytics_id', $analyticsSession->id);


		/*
		|--------------------------------------------------------------------------
		| Record Page Views
		|
		*/

		//For php versions less than 5.4
		if (!function_exists('http_response_code')) {
			function http_response_code()
			{
				if(isset($_SERVER['REDIRECT_STATUS']))
				{
					return (int) $_SERVER['REDIRECT_STATUS'];
				}
				else
				{
					return false;
				}
			}
		}

		// Generate a new page view
		$pageView = new PageView;

		// Save the visitoranalytics id so we can link the data
		$pageView->visitoranalytics_id = $analyticsSession->id;

		// HTTP Response Code: eg 200, 404, etc
		$pageView->status              = http_response_code();

		// Save the URL of the page view
		$pageView->url                 = Request::path() === '/' ? '/' : '/' . Request::path();

		// Check to see if the call is an ajax call
		$pageView->ajax                = Request::ajax();

		// Get the request method: GET, POST, PUT, DELETE
		if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'])
		{
			$pageView->method          = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		// Save the referrer so we can track a users through the site
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'])
		{
			$pageView->referrer        = $_SERVER['HTTP_REFERER'];
		}

		// And save
		$pageView->save();
	} catch (Exception $e) { }

});
