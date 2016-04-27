ShopApp.controller('ShopProductListCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	//clean
	$scope.cleanProductList = function(){
		$scope.products = [];
		//Pag
		$scope.totalItems = 0;
		$scope.currentPage = 0;	
		$scope.radioModel = '10';
		$scope.maxSize = 10;	
		//Checkbox
		$scope.selected_items = 0;
	};
	//Lista de itens
	$scope.initProductList = function(search){//Carrega todos os itens
		search = typeof search !== 'undefined' ? search : $scope.searchText;
		isSpinnerBar(true);	

		ShopSrvc.getListProductList(search, $scope.radioModel, $scope.currentPage).then(function(res){
			if(res.status == true){
				$timeout(function(){
					$scope.$apply(function(){
						$scope.products = res.data.items;
						$scope.totalItems = res.data.total;
						$scope.currentPage = res.data.offset;
					});
				});
			}else{
				$scope.cleanProductList();
			}
			
			$timeout(function(){ isSpinnerBar(false);}, 500);
		});
	};	
	//Shop Product Details
	$scope.goDetails = function(id){
		isSpinnerBar(true);	
		$localStorage.ProductDetailsId = id;
		console.log(id);
		$timeout(function(){
			$scope.changeTemplateURL('/shop-product-details');
		}, 500);		
	};
	/**
	 * Default
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});