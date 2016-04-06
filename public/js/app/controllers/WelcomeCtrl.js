ShopApp.controller('WelcomeCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	$timeout(function(){
		isApp();
	},100);
	
	$timeout(function(){
		isPageScripts();
	},300);	
	
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});