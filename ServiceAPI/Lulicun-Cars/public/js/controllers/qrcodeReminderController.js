'use strict';
app.controller('QRCodeReminderCtrl', ['$scope', '$rootScope', '$routeParams', 'requestService',
	function($scope, $rootScope, $routeParams, requestService) {
		console.log("QRCodeReminderCtrl");
		var contact = $routeParams.id.split("_");
		var contactType = contact[0];
		var contactContent = contact[1];
		
		$scope.buttonName = (contactType === "tele") ? "Text" : "Email";
		console.log("Received: ", contactType, contactContent);
		$scope.isSent = false;
		$scope.sendResult = 'Click to Send';
		$scope.sendReminder = function(){
			requestService.sendReminder(contactContent, function(data) {
				console.log(data);
				$scope.isSent = true;
				$scope.sendResult = data;
			});
			
		};
	}
]);