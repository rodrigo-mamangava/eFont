ShopApp.controller('ProfileCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//form
	$scope.form = {};
	/**
	 * SAVE
	 */
	$scope.saveProfile = function (){
		isSpinnerBar(true);	
		ShopSrvc.saveProfile($scope.form).then(function(res){
			if(res.status == true){
				$scope.changeTemplateURL('/shop-customer/index');
			}else{
				bootbox.alert(res.data);
				$timeout(function(){ isSpinnerBar(false);}, 500);
			}
		});
	};
	/**
	 * GET
	 */
	$scope.getProfile = function(){
		isSpinnerBar(true);	
		ShopSrvc.getProfile().then(function(res){
			if(res.status == true){
				$timeout(function(){
					$scope.form = res.data;
					setSelect2me('address_country',$scope.form.address_country, true);
				});
			}
		});		
		$timeout(function(){ isSpinnerBar(false);}, 1000);
	};
	/**
	 * Default
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});