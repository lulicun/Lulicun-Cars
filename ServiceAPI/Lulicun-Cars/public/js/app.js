'use strict';

var app = angular.module('Lulicun', ['ngRoute']);

app.config(['$routeProvider',
	function($routeProvider){
		$routeProvider.
			when('/home', {
				templateUrl: 'views/home.html',
				controller: 'HomeCtrl'
			}).
			when('/', {
				redirectTo: '/qrcodeGenerater'
			}).
			when('/qrcodeReminder/:id', {
				templateUrl: 'views/qrcodeReminder.html',
				controller: 'QRCodeReminderCtrl'
			}).
			when('/qrcodeGenerater', {
				templateUrl: 'views/qrcodeGenerater.html',
				controller: 'QRCodeGeneraterCtrl'
			}).						
			otherwise({
				redirectTo: '/qrcodeGenerater'
			});
	} 
]);