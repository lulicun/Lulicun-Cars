'use strict';

app.factory('requestService', ['$rootScope', '$http', 'apiEndpoint', 'apiVersion',
	function($rootScope, $http, apiEndpoint, apiVersion) {
		$http.defaults.useXDomain = true;
		var apiEndPointUrl = apiEndpoint + apiVersion;
		return {
			getQRCode: function(data, callback) {
				$http.get('qrcode/get-qrcode?contact=' + data).
	  				success(function(data, status, headers, config) {
	  					if (!data.webUrl) {
	  						//TODO: show err on page
	  						console.log("err: ", data.err);
	  					} else {
	  						console.log(data);
	  						callback(data.webUrl);	
	  					}
						
					}).
					error(function(data, status, headers, config) {

					}
				);
			},
			sendReminder: function(data, callback) {
				$http.get('qrcode/send-reminder?encryptedContact=' + data).
	  				success(function(data, status, headers, config) {
						callback(data.result);
					}).
					error(function(data, status, headers, config) {

					}
				);
			}
		}
		
	}
])