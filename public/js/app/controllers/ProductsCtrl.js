ShopApp.controller('ProductsCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.form = {};
	//clean
	$scope.cleanProducts = function(){
		$scope.products = [];
		$scope.families = [];
		$scope.licenses = [];
		$scope.formats = [];
		//Pag
		$scope.totalItems = 0;
		$scope.currentPage = 0;	
		$scope.radioModel = '10';
		$scope.maxSize = 10;	
		//Checkbox
		$scope.selected_items = 0;
	};
	//Lista de itens
	$scope.initProducts = function(search){//Carrega todos os itens
		search = typeof search !== 'undefined' ? search : $scope.searchText;
		isSpinnerBar(true);	
		
		ShopSrvc.getListProducts(search, $scope.radioModel, $scope.currentPage).then(function(res){
			if(res.status == true){
				$timeout(function(){
					$scope.$apply(function(){
						$scope.products = res.data.items;
						$scope.totalItems = res.data.total;
						$scope.currentPage = res.data.offset;
					});
				});
			}else{
				$scope.cleanProducts();
			}
			
			$timeout(function(){ isSpinnerBar(false);}, 500);
		});
	};	
	//Salvando
	$scope.saveProducts = function (){
		isSpinnerBar(true);	
		ShopSrvc.saveProducts($scope.form).then(function(res){
			if(res.status == true){
				$scope.changeTemplateURL('/ef-products');
			}else{
				bootbox.alert(res.data);
				$timeout(function(){ isSpinnerBar(false);}, 500);
			}
		});
	};
	//Edicao
	$scope.editProducts = function(id){
		isSpinnerBar(true);	
		$localStorage.ProductsId = id;
		$timeout(function(){
			$scope.changeTemplateURL('/ef-products/form');
		}, 500);
	};
	/**
	 * Carrega o produto
	 */
	$scope.getProducts = function(){
		isSpinnerBar(true);	
		//Licencas ativas
		ShopSrvc.getListActiveLicenses().then(function(res){
			if(res.status == true){
				$scope.licenses = res.data.items;
				
				//Carregando formatos
				//Formatos disponivel
				ShopSrvc.getListFormats().then(function(res){
					if(res.status == true){
						$scope.formats = res.data.items;

						//Carregando produtos
						$timeout(function(){
							if(!isBlank($localStorage.ProductsId)){
								var id = $localStorage.ProductsId;

								ShopSrvc.getProducts(id).then(function(res){
									if(res.status == true){
										$scope.form = res.data;
									}else{
										bootbox.alert(res.data);
										$scope.form = {};
									}
								});		
							}else{
								//$scope.form = {};
								$timeout(function(){
									$scope.form = {name: ipsumService.words(5)};
									$scope.addFamilyItem();
								}, 500);
							}
							$timeout(function(){ delete $localStorage.ProductsId; }, 500);
							$timeout(function(){ 
								isSpinnerBar(false);
								isDropzone();
							}, 500);
						}, 500);						
					}else{
						bootbox.alert(res.data);					
					}
				});
			}else{
				bootbox.alert(res.data, function(){
					$timeout(function(){
						$scope.changeTemplateURL('/ef-licenses/form');
					}, 500);					
				});
			}
		});
	};
	//Add Files
	$scope.getRetrieveDetails = function(f_key, t_key ,t_id){
		isSpinnerBar(true);
		var zip = $('#media_url'+f_key+''+t_key).val();
		if(zip.length > 10){
			var data = {uploaded: zip};
			ShopSrvc.getListFontFiles(data).then(function(res){
				if(res.status == true){
					$timeout(function(){
						$scope.$apply(function(){
							$scope.families[f_key].formats[t_id].files = res.data.files;
							$scope.families[f_key].formats[t_id].number_files = res.data.total;
						});
					});
					
					
					console.log($scope.families);
				}else{
					bootbox.alert(res.data);
				}
				$timeout(function(){
					isSpinnerBar(false);
				});
			});
		}else{
			isSpinnerBar(false);
		}
	};
	//Add Familias
	$scope.addFamilyItem = function(){
		$scope.families.push({formats:[], licenses:[{}],family_name:ipsumService.words(5)});
		console.log($scope.families);
	};
	//Remove uma Familia
	$scope.removeFamilyItem = function(f_key, y, n, t, m, err){
		console.log(f_key);
		bootbox.dialog({
			title: t,
			message: m,
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
								$scope.families.splice(f_key, 1);		
							});
						});
					}
				},				
			}
		});			
	};	
	//Remover varios itens
	$scope.removeSelected = function(y, n, t, m, err) {
		var html = '';
		if($scope.selected_items < 1){
			bootbox.alert(err);
		}else{
			angular.forEach($scope.products, function (item, k) {
				if(item.Selected === true){
					html = html +'<b class="has-error">'+item.name +'</b><br/>';
				}
			});

			bootbox.dialog({
				title: t,
				message: m+' : <p>'+html+'</p>',
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
							isSpinnerBar(true);
							angular.forEach($scope.products, function (item, k) {
								if(item.Selected === true){
									$scope.removed(item.id);
								}
							});
						}
					},				
				}
			});	
		}
	};  
	//executa a acao de remover
	$scope.removed = function(id){
		isSpinnerBar(true);
		ShopSrvc.removeProducts(id).then(function(res){
			if(res.status == true){
				$('#products-tr-'+id).addClass('hide');
				isSpinnerBar(false);
			}else{
				$('#products-tr-'+id).addClass('danger');
				isSpinnerBar(false);
			}									
		});
	};	
	
	//Alterando paginacao
	$scope.pageChanged = function (){ $scope.initProducts(); }
	//Select all
	$scope.checkAll = function () {
		if ($scope.selectedAll) {
			$scope.selectedAll = true;
			$scope.selected_items = $scope.totalItems;
		} else {
			$scope.selectedAll = false;
			$scope.selected_items = 0;
		}
		angular.forEach($scope.products, function (item) {
			item.Selected = $scope.selectedAll;
		});

	};
	//Se selecionado
	$scope.isSelected = function(id) {
		if ($('#'+id).is(':checked')) {
			$scope.selected_items++;
		}else{
			$scope.selected_items--;
		}
	};	
	//Init
	$scope.cleanProducts();
	$('#qz-query').keypress(function(e) {
		if(e.which == 13) {
			$scope.initProducts($('#qz-query').val());
		}
	});	
	/**
	 * Not implement alert
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
});