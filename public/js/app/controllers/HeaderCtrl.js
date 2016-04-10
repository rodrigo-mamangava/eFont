ShopApp.controller('HeaderCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});