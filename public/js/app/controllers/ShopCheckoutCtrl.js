ShopApp.controller('ShopCheckoutCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.completed = [];
	$scope.yourorder = '';
	$scope.checkout = '';
	$scope.discount = 0;
	$scope.total = 0;
	$scope.purchased = new Date(); 
	/**
	 * Inicializando Order view
	 */
	$scope.initCheckoutCompleted = function(){
		$scope.completed = $localStorage.ShopCompleted;
		$scope.yourorder = $localStorage.ShopYourOrder;
		$scope.checkout  = $localStorage.ShopCheckout;
		$scope.total = $scope.checkout.total |0 ;
		
		console.log($scope.completed);
		console.log($scope.yourorder);
		console.log($scope.checkout);
		
		isSpinnerBar(true);
		$timeout(function(){
			isSpinnerBar(false);
			
			delete $localStorage.ShopCompleted;
			delete $localStorage.ShopYourOrder;
			delete $localStorage.ShopCheckout;
			delete $localStorage.ShopYourCart;
		},100);
	};
	/**
	 * Default
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});