ShopApp.controller('ShopProductDetailsCtrl', function($scope, $timeout, $http, $localStorage, ShopSrvc) {
	//global
	$scope.searchText = '';	
	//clean
	$scope.cleanProductDetails = function(){
		$scope.form = {};
		$scope.products = [];
		$scope.licenses = [];
		$scope.formats = [];
		$scope.families = [];
		$scope.collections = [];
		$scope.license = 0;
		$scope.format = '';
		$scope.current = 0;
		$scope.cart = 0.00;
	};
	//Obtem o item
	$scope.getProductDetails = function(){
		if(!isBlank($localStorage.ProductDetailsId)){
			isSpinnerBar(true);	
			var id = $localStorage.ProductDetailsId;
			
			ShopSrvc.getProductDetails(id).then(function(res){
				if(res.status == true){
					
					$timeout(function(){
						$scope.$apply(function(){
							$scope.form = res.data.project;
							$scope.license = $scope.form.license;  
							$scope.format = $scope.form.format;
							
							$scope.licenses = res.data.licenses;
							$scope.formats = res.data.formats;
							$scope.families = res.data.families;
							$scope.collections = res.data.collections;
							
							console.log(res.data);
							$timeout(function(){ 
								isSpinnerBar(false);
								$scope.pricebook();
							}, 500);
						});
					});
				}else{
					bootbox.alert(res.data);
					$scope.form = {};
					$timeout(function(){ isSpinnerBar(false);}, 500);
				}
			});		
		}else{
			$scope.form = {};
		}
		//$timeout(function(){ delete $localStorage.ProductDetailsId; }, 500);
	};
	/**
	 * Licencas
	 */
	$scope.onChangeLicense = function (lu_key, lu_id){
		$scope.license = lu_key;
		$scope.current = lu_id;
		console.log(lu_key, lu_id);
		//Recalculando precos
		$scope.pricebook();
	};
	/**
	 * Formatos/Multiplicadores
	 */
	$scope.onChangeLicenseFormats = function (ll_key, ll_id, ft_id){
		console.log(ll_key, ll_id, ft_id);
		console.log($scope.format[ll_key][ft_id]);
		$timeout(function(){
			$scope.pricebook();
		},50);
	};
	/**
	 * PRICEBOOK
	 */
	$scope.pricebook = function(){
		angular.forEach($scope.format[$scope.license], function(ft_value, ft_key) {
			var price = $scope.collections[$scope.license][ft_key][ft_value];
			angular.forEach(price, function(f_value, f_key) {
				$timeout(function(){
					$scope.families[f_key].collection = parseFloat(f_value) + parseFloat($scope.families[f_key].collection);	
				});
			});
		});	
	};
	/**
	 * Clean
	 */
	$scope.syncprice = function(){
		
	};
	/**
	 * Default
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};	
	//Init
	$scope.cleanProductDetails();
});