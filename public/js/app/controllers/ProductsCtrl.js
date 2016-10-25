ShopApp.controller('ProductsCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';
	$scope.form = {licenses:[]};
	//clean
	$scope.cleanProducts = function(){
		$scope.products = [];
		$scope.families = [];
		//$scope.families = [{"formats":[],"licenses":[{}],"family_name":ipsumService.words(5)},{"formats":[],"licenses":[{}],"family_name":ipsumService.words(5)}];
		$scope.licenses = [];
		$scope.formats = [];
		$scope.price=[];
		$scope.current_price_family = 0;
		//Form
		$scope.screen_from = true;
		$scope.screen_summary = false;
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
		$scope.form.number_families = $scope.families.length;
		var data = {'project':$scope.form, 'families':$scope.families};
		ShopSrvc.saveProducts(data).then(function(res){
			if(res.status == true){
				$scope.changeTemplateURL('/ef-products');
			}else{
				bootbox.alert(res.data);
				$timeout(function(){ isSpinnerBar(false);}, 500);
			}
		});

		console.log(angular.toJson(data));
	};
	//Licenca
	$scope.setLicenseId = function(l_key, l_id){
		$scope.form.licenses[l_key].license_id =l_id;
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
				angular.forEach($scope.licenses, function (item, key) {
					$scope.form.licenses[key] ={license_id:item.id};
				});
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
										$scope.form = res.data.project;
										$scope.families = res.data.families;
									}else{
										bootbox.alert(res.data);
										$scope.form = {};
									}
								});
							}else{
								//$scope.form = {licenses:[]};
							}
							$timeout(function(){ delete $localStorage.ProductsId; }, 500);
							$timeout(function(){
								isSpinnerBar(false);
								try{
									isDropzone();
								}catch(err){
									console.log(err);
								}
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
			var data = {uploaded: zip, 'f_key':f_key, 't_key':t_key, 'format_id':t_id};
			ShopSrvc.getListFontFiles(data).then(function(res){
				if(res.status == true){
					$timeout(function(){
						$scope.$apply(function(){
							$scope.families[f_key].formats[t_id].files = res.data.files;
							$scope.families[f_key].formats[t_id].number_files = res.data.total;

							if(isBlank($scope.form.ddig)){
								$scope.form.ddig = res.data.ddig;
							}

							$timeout(function(){
								$scope.updateWeight(f_key, t_key , -1);
							},20);
						});
					});
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
	//Atualizando precos
	$scope.updateWeight = function(f_key, l_key, l_id){
		var price = $scope.families[f_key].money_weight;
		angular.forEach($scope.families[f_key].formats, function (f_item, f_k) {
			angular.forEach(f_item.files, function (fl_item, fl_k) {
				if(fl_item.check_price == false){
					fl_item.font_price = price;
				};
			});
		});
	};
	//Atualizando preco de familia
	$scope.updateFamilyPrice = function(l_key){
		var price = $scope.form.licenses[l_key].money_family;
		$scope.current_price_family = price;
		angular.forEach($scope.families, function (f_item, f_k) {
			//console.log(f_item);
			if(f_item.check_family == false){
				$scope.families[f_k].money_family = price;
			};
		});
	};
	//Add Familias
	$scope.addFamilyItem = function(){
		console.log( "$scope.families.length: " + $scope.families.length );
		if ( $scope.families.length < 1 ){
			$scope.families.push({formats:[], family_name:null, money_family: $scope.current_price_family, check_family: false});
		}
	};
	//Remove uma Familia
	$scope.removeFamilyItem = function(f_key, y, n, t, m, err){
		//console.log(f_key);
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

				if($scope.products.length == 0){
					$scope.initProducts();
				}
				isSpinnerBar(false);
			}else{
				$('#products-tr-'+id).addClass('danger');
				isSpinnerBar(false);
			}
		});
	};
	//Retornando a tela inicial
	$scope.goBack = function(y, n, t, m, err){
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
						isSpinnerBar(true);
						$timeout(function(){
							$scope.changeTemplateURL('/ef-products');
						},500);
					}
				},
			}
		});
	};
	/**
	 * Exibe o summary, oculta o form
	 */
	$scope.goSummary = function(){
		$scope.screen_from = false;
		$scope.screen_summary = true;
	};
	/**
	 * Exibe o form, oculta o summario
	 */
	$scope.goForm = function(){
		$scope.screen_from = true;
		$scope.screen_summary = false;
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