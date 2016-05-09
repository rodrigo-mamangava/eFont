ShopApp.controller('ShopCheckoutCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.checkout = [];
	$scope.subtotal = 0;
	$scope.discount = 0;
	$scope.total = 0;
	/**
	 * Inicializando carrinho
	 */
	$scope.initCheckoutCart = function(){
		if(!isBlank($localStorage.ShopYourCart)){
			isSpinnerBar(true);
			$timeout(function(){
				$scope.checkout = $localStorage.ShopYourCart;
				$scope.pricebook(); 
				isSpinnerBar(false);
			},100);
		}else{
			delete $scope.checkout;
		}	
	};
	/**
	 * Default
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});