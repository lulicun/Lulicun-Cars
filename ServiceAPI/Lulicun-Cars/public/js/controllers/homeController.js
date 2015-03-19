'use strict';

app.controller('HomeCtrl', ['$scope', '$rootScope', '$location',
	function($scope, $rootScope, $location){
		console.log("homeController.js");
		$scope.goToQRCodeGenerater = function(){
			$location.path('qrcodeGenerater');
		};
	}
]);