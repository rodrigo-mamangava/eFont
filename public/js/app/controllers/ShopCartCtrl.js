ShopApp.controller('ShopCartCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.cart = [];
	$scope.subtotal = 0;
	$scope.discount = 0;
	$scope.total = 0;
	$scope.form = {};
	/**
	 * Limpando/inicializando
	 */
	$scope.cleanCart = function(){
		$scope.form.payment = 0;
	};
	/**
	 * Inicializando carrinho
	 */
	$scope.initYourCart = function(){
		if(!isBlank($localStorage.ShopYourCart)){
			isSpinnerBar(true);
			$timeout(function(){
				$scope.cart = $localStorage.ShopYourCart;
				$scope.pricebook(); 
				isSpinnerBar(false);
			},100);
		}else{
			delete $scope.cart;
		}	
	};
	/**
	 * Remove item
	 */
	$scope.removeItem = function(key, y, n, t, m, err){
		bootbox.dialog({
			title: t,
			message: m + ' : <b>'+$scope.cart[key].form.name+'</b>',
			buttons: {
				success: {
					label: n,
					className: "btn-default",
					callback: function() {}
				},
				danger: {
					label: y,
					className: "btn-danger",
					callback: function() {
						$timeout(function(){
							$scope.$apply(function(){
								$scope.cart.splice(key, 1);
								$timeout(function(){
									$localStorage.ShopYourCart = $scope.cart;
									$scope.pricebook();
								},10);
							});
						});
					}
				},				
			}
		});	
	};
	/**
	 * pricebook
	 */
	$scope.pricebook = function(){
		$scope.subtotal = 0;
		angular.forEach($scope.cart, function(c_item, c_key) {
			$scope.subtotal = parseFloat($scope.subtotal) + parseFloat(c_item.cart);
		});
		
		$scope.total = parseFloat($scope.subtotal) - parseFloat($scope.discount);
		$('#shop-c-cart-number').html($scope.cart.length).trigger('change');
	};
	/**
	 * Default
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
	/**
	 * View Cart
	 */
	$scope.viewCart = function(){
		$scope.UpdateViewCart();
		$('#shop-c-cart-number').change(function() {
			$scope.UpdateViewCart();
		});
	};
	/**
	 * Atualizando view do menu superior
	 */
	$scope.UpdateViewCart = function(){
		$scope.cart = $localStorage.ShopYourCart;

		$timeout(function(){
			$scope.subtotal = 0;
			angular.forEach($scope.cart, function(c_item, c_key) {
				$scope.subtotal = parseFloat($scope.subtotal) + parseFloat(c_item.cart);
			});
			
			$scope.total = parseFloat($scope.subtotal) - parseFloat($scope.discount);			
		},10);
	};
	/**
	 * Go to Checkout
	 */
	$scope.goCheckoutCompleted = function(){
		isSpinnerBar(true);
		var data = {'checkout': $scope.form, 'cart': $scope.cart};
		console.log(data);
		ShopSrvc.goCheckoutCompleted(data).then(function(res){
			console.log(res);
			if(res.status == true){
				$scope.form.total = $scope.total;
				$localStorage.ShopCompleted =  res.data;
				$localStorage.ShopYourOrder =  res.outcome;
				$localStorage.ShopCheckout = $scope.form;
				
				delete $localStorage.ShopYourCart;
				
				$timeout(function(){
					$scope.changeTemplateURL('/shop-checkout-complete');
					$timeout(function(){ isSpinnerBar(false);}, 500);
				},100);
			}else{
				bootbox.alert(res.data);
				$timeout(function(){ isSpinnerBar(false);}, 500);
			}
		});	
	};
	//init
	$scope.cleanCart();
});