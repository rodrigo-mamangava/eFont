ShopApp.controller('CompanyProfileCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//form
	$scope.form = {};
	/**
	 * SAVE
	 */
	$scope.saveCompanyProfile = function (){
		isSpinnerBar(true);	
		ShopSrvc.saveCompanyProfile($scope.companyData).then(function(res){
			if(res.status == true){
				bootbox.alert(res.data);
				$timeout(function(){
					bootbox.hideAll();
				},2000);
			}else{
				bootbox.alert(res.data);
			}
			$timeout(function(){ isSpinnerBar(false);}, 500);
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