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
		$scope.formats = [[],[],[],[],[]];
		$scope.price=[];
		$scope.cart = 0.00;
		$scope.styles = [];
		$scope.current ='';
		$scope.multiplier = [];
		$scope.multiplier_c = 1;
		$scope.data = [];
		$scope.preload = [];
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
							$scope.preload = res.data.preload;
							
							$timeout(function(){
								$scope.license = 0;
								$scope.multiplier = $scope.preload.multiplier;
								$scope.onChangeLicense(0, $scope.licenses[0].id);
								$timeout(function(){
									$scope.reloadFormats();	
								}, 200);								
							}, 100);
							
							//console.log(res.data);
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
		//$timeout(function(){ delete $localStorage.ProductDetailsId; }, 500);
	};
	//Alterando a licenca
	$scope.onChangeLicense = function(lu_key, lu_id){
		$scope.license = lu_key;
		$scope.current = lu_id;
		
		$scope.form.collection = 0;
		angular.forEach($scope.families, function(value, key) {
			$scope.form.collection = parseFloat(value.licenses[lu_id].money_family) + parseFloat($scope.form.collection);
		});
		//Recarrega lista
		$scope.reloadFormats();
	};
	
	//Alterando formato da licenca
	$scope.onChangeLicenseFormats= function(ll_key, ll_id, type){
		console.log('onChangeLicenseFormats');
		//Restaura valores originais	
		//$scope.onChangeTypes(ll_key, ll_id, type);
		//Calculando
		$timeout(function(){
			//$scope.multiplier[ll_key][type] = $scope.licenses[ll_key].formats[type][$scope.formats[ll_key][type]].multiplier;
			//console.log($scope.licenses[ll_key].formats[type][$scope.formats[ll_key][type]].multiplier);
			$scope.onSyncCart();
		}, 400);
	};
	//Recarregando formatos
	$scope.reloadFormats =  function(){
		angular.forEach($scope.preload.formats, function(lc, k) {
			angular.forEach(lc, function(fr, i) {
				$scope.formats[k][i] = fr;				
				$scope.onChangeLicenseFormats(k, fr, i);
				$scope.onChangeTypes(k, lc.id, i);
			});
		});		
	};
	//Alterando tipos de licencas
	$scope.onChangeTypes = function(ll_key, ll_id, type){
		//console.log('onChangeTypes');
		
		if($scope.licenses[ll_key].types_desktop == true && type == 1){
			$scope.onChangeStyles(ll_key, ll_id, type);
			
		}else if($scope.licenses[ll_key].types_web == true && type == 2){
			$scope.onChangeStyles(ll_key, ll_id, type);
			
		}else if($scope.licenses[ll_key].types_app == true && type == 3){
			$scope.onChangeStyles(ll_key, ll_id, type);
			
		}else{
			$scope.cleanStyles(type);
		}
		
		$scope.onSyncCart();
	};
	//Collection
	$scope.uncheckingAllCollection = function(){
		angular.forEach($scope.families, function(f, k) {
			f.check_collection =  false;
		});
	};
	//Selecione um collection
	$scope.onChangeSelectedCollection = function(f_key, f_id){
		console.log(f_key, f_id);
		$scope.collection = true;
		
		$scope.uncheckingAllItems();
		$scope.onSyncCart();
	};	
	//Desmarcando pesos
	$scope.uncheckingAllItems = function(){
		//Zera cart para evitar problema
		angular.forEach($scope.families, function(f, k) {
			$scope.selected_numbers[k] = 0;
			angular.forEach(f.styles, function(s, t) {
				angular.forEach(s, function(y, e) {
					$scope.families[k].styles[t][e].selected = false;
				});
			});
		});
	};
	//Selecionando um item
	$scope.onChangeSelectedItem = function(f_s_key, f_key, type ){
		//Verificando collection
		if($scope.collection == true){
			$scope.price = 0;
			$scope.collection =  false;
			$scope.uncheckingAllCollection();
		}
		//Calculando pesos
		var number = $scope.selected_numbers[f_key] || 0;
		//var price = $scope.cart;
		
		if($scope.families[f_key].styles[type][f_s_key].selected){
			$scope.selected_numbers[f_key] = number + 1;
			//$scope.cart = parseFloat(price) +(parseFloat($scope.families[f_key].styles[type][f_s_key].font_weight) * parseFloat($scope.multiplier[type]));
			$scope.onSyncCart();
		}else{
			$scope.selected_numbers[f_key] = number - 1;
			//$scope.cart =  parseFloat(price) - (parseFloat($scope.families[f_key].styles[type][f_s_key].font_weight) * parseFloat($scope.multiplier[type]));
			$scope.onSyncCart();
		}
	};	
	//Sync Cart - Sincroniza o preco total
	$scope.onSyncCart = function(){		
		$scope.multiplier_c = 0;
		if($scope.licenses[$scope.license].types_desktop == true){
			$scope.multiplier_c = parseFloat($scope.multiplier[$scope.license][1][$scope.formats[$scope.license][1]]);
			console.log(1);
		}

		if($scope.licenses[$scope.license].types_web == true){
			$scope.multiplier_c = $scope.multiplier_c + parseFloat($scope.multiplier[$scope.license][2][$scope.formats[$scope.license][2]]);
			console.log(2);
		}

		if($scope.licenses[$scope.license].types_app == true){
			$scope.multiplier_c = $scope.multiplier_c + parseFloat($scope.multiplier[$scope.license][3][$scope.formats[$scope.license][3]]);
			console.log(3);
		}

		$timeout(function(){
			if($scope.collection == false){
				$scope.cart = 0;
				angular.forEach($scope.families, function(f_item, f_key) {
					angular.forEach(f_item.styles, function(s_item, type) {
						angular.forEach(s_item, function(fo_item, f_s_key) {
							if(fo_item.selected == true){
								//$scope.cart = parseFloat($scope.cart) +(parseFloat($scope.families[f_key].styles[type][f_s_key].font_weight) * parseFloat($scope.multiplier[type]));	
								var m = parseFloat($scope.multiplier[$scope.license][type][$scope.formats[$scope.license][type]]);
								var w = parseFloat($scope.families[f_key].styles[type][f_s_key].font_weight);
								var p = parseFloat($scope.cart);
								$scope.cart = p+(w*m);
							}
						});
					});
				});
			}else{
				$scope.cart = 0;
				angular.forEach($scope.families, function(f_item, f_key) {
					if(f_item.check_collection == true){
						var id = $scope.licenses[$scope.license].id;
						var w = parseFloat($scope.preload.collection[f_key][id]);
						var m = parseFloat($scope.multiplier_c);
						$scope.cart = w * m;
					}
				});
			}			
		},50);
	};	
	//SetStyles
	$scope.onChangeStyles = function(ll_key, ll_id, type){
		console.log('onChangeTypes');
		$scope.cleanStyles(type);
		$timeout(function(){
			angular.forEach($scope.families, function(f, k) {
				if(typeof f.styles[type] !== "undefined"){
					$scope.styles[k][type] = f.styles[type]; 	
					$scope.families[k].collapsed = true;
					//console.log($scope.styles[k][type]);
				}
			});					
		}, 500);
	};
	//CleanStyles
	$scope.cleanStyles = function(type){
		//console.log('cleanStyles');
		//$scope.styles.length = 0; 
		angular.forEach($scope.families, function(f, k) {
			delete $scope.styles[k][type];
		});			
	};
	//Default Styles
	$scope.defaultStyles = function(type){
		//console.log('defaultStyles');
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