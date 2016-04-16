ShopApp.controller('ProductsCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	/**
	 * Not implement alert
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});