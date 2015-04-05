'use strict';

app.controller('QRCodeGeneraterCtrl', ['$scope', '$rootScope', 'requestService',
	function($scope, $rootScope, requestService) {
		console.log("QRCodeGeneraterCtrl");
		
		$scope.getQRCode = function(){
			if (!$scope.contact)
				return;
			requestService.getQRCode($scope.contact, function(qrcodeUrl) {
				$scope.qrcodeUrl = qrcodeUrl;
				console.log(qrcodeUrl);
			});
		};
	}
]);