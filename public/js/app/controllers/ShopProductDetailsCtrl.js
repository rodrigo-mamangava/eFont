ShopApp.controller('ShopProductDetailsCtrl', function($scope, $timeout, $http, $localStorage, ShopSrvc) {
	//global
	$scope.searchText = '';	
	//clean
	$scope.cleanProductDetails = function(){
		$scope.form = {};
		$scope.products = [];
		$scope.families = [];
		$scope.licenses = [];
		$scope.license = 0;
		$scope.formats = [];
		$scope.price=[];
		$scope.cart = 0.00;
		$scope.styles = [];
		$scope.current ='';
		$scope.multiplier = [[1],[1],[1],[1]];
		$scope.data = [];
		$scope.collection = false;
		$scope.selected_itens = [];
		$scope.selected_numbers = [];
		//Form
		$scope.screen_from = true;
		$scope.screen_summary = false;		
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
							$scope.licenses = res.data.licenses;
							$scope.families = res.data.families;
							$scope.data = res.data;
							
							console.log(res.data);
							$timeout(function(){ isSpinnerBar(false);}, 500);
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
		$timeout(function(){ delete $localStorage.ProductDetailsId; }, 500);
	};
	//Alterando a licenca
	$scope.onChangeLicense = function(lu_key, lu_id){
		$scope.license = lu_key;
		$scope.current = lu_id;
		
		$scope.form.collection = 0;
		angular.forEach($scope.families, function(value, key) {
			$scope.form.collection = parseFloat(value.licenses[lu_id].money_family) + parseFloat($scope.form.collection);
		});
	};
	
	//Alterando formato da licenca
	$scope.onChangeLicenseFormats= function(ll_key, ll_id, type){
		console.log('onChangeLicenseFormats');
		//Restaura valores originais	
		$scope.onChangeTypes(ll_key, ll_id, type);
		//Calculando
		$timeout(function(){
			$scope.multiplier[type] = $scope.licenses[ll_key].formats[type][$scope.formats[ll_key][type]].multiplier;
		}, 500);
		//Zera cart para evitar problema
		angular.forEach($scope.families, function(f, k) {
			$scope.selected_numbers[k] = 0;
			angular.forEach(f.styles, function(s, t) {
				angular.forEach(s, function(y, e) {
					$scope.families[k].styles[t][e].selected = false;
				});
			});
		});
		$scope.cart = 0;
	};
	//Alterando tipos de licencas
	$scope.onChangeTypes = function(ll_key, ll_id, type){
		console.log('onChangeTypes');
		
		if($scope.licenses[ll_key].types_desktop == true && type == 1){
			$scope.onChangeStyles(ll_key, ll_id, type);
			
		}else if($scope.licenses[ll_key].types_web == true && type == 2){
			$scope.onChangeStyles(ll_key, ll_id, type);
			
		}else if($scope.licenses[ll_key].types_app == true && type == 3){
			$scope.onChangeStyles(ll_key, ll_id, type);
			
		}else{
			$scope.cleanStyles(type);
		}
	};
	//Collection
	$scope.onChangeCollection = function(){
		console.log($scope.collection);
		if($scope.collection == true){
			$scope.cart = $scope.form.collection;
		}else{
			$scope.cart = 0;
		}
	};
	//Selecionando um item
	$scope.onChangeSelectedItem = function(f_s_key, f_key, type ){
		//Verificando collection
		if($scope.collection == true){
			$scope.price = 0;
			$scope.collection =  false;
		}
		
		//Calculando pesos
		var number = $scope.selected_numbers[f_key] || 0;
		var price = $scope.cart;
		
		if($scope.families[f_key].styles[type][f_s_key].selected){
			$scope.selected_numbers[f_key] = number + 1;
			$scope.cart = parseFloat(price) +(parseFloat($scope.families[f_key].styles[type][f_s_key].font_weight) * parseFloat($scope.multiplier[type]));
		}else{
			$scope.selected_numbers[f_key] = number - 1;
			$scope.cart =  parseFloat(price) - (parseFloat($scope.families[f_key].styles[type][f_s_key].font_weight) * parseFloat($scope.multiplier[type]));
		}
	};
	//SetStyles
	$scope.onChangeStyles = function(ll_key, ll_id, type){
		console.log('onChangeTypes');
		$scope.cleanStyles(type);
		
		$timeout(function(){
			angular.forEach($scope.families, function(f, k) {
				if(f.styles[type] !== undefined){
					$scope.styles[k][type] = f.styles[type]; 	
					$scope.families[k].collapsed = true;
				}
			});					
		}, 500);
	};
	//CleanStyles
	$scope.cleanStyles = function(type){
		console.log('cleanStyles');
		//$scope.styles.length = 0; 
		angular.forEach($scope.families, function(f, k) {
			delete $scope.styles[k][type];
		});			
	};
	//Default Styles
	$scope.defaultStyles = function(type){
		console.log('defaultStyles');
		$scope.styles.length = 0; 
		angular.forEach($scope.families, function(f, k) {
			$scope.styles[k] = [[],[],[],[]];
			$scope.families[k].collapsed = false;
		});			
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