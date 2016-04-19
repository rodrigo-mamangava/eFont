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
		$scope.price=[];
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
		//console.log(angular.toJson($scope.families));
		isSpinnerBar(true);	
		var data = {'project':$scope.form, 'families':$scope.families};data
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
										$scope.form = res.data.project;
										$scope.families = res.data.families;
									}else{
										bootbox.alert(res.data);
										$scope.form = {};
									}
								});		
							}else{
								$scope.form = {};
								/*$timeout(function(){
									$scope.form = {name: ipsumService.words(5)};
									$scope.addFamilyItem();
								}, 500);*/
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
					
					
					$timeout(function(){
						console.log($scope.families);
						console.log(angular.toJson($scope.families));
					},2000);
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
		console.log(f_key, l_key, l_id);
		console.log($scope.families[f_key].licenses[l_key]);
		
		var price = $scope.families[f_key].licenses[l_key].money_weight;
		
		angular.forEach($scope.families[f_key].formats, function (f_item, f_k) {
			angular.forEach(f_item.files, function (fl_item, fl_k) {
				if(fl_item.check_price == false){
					fl_item.font_price = price; 
				};
			});
		});
	}; 
	//Add Familias
	$scope.addFamilyItem = function(){
		//$scope.families.push({formats:[], licenses:[{}],family_name:ipsumService.words(5)});
		$scope.families.push({"formats":[{"media_url":"https://s3-us-west-2.amazonaws.com/aprepara/contents/r853v2281ejz3qgoqkf1srcxb6dihrm3.zip","files":[{"font_name":"ChunkFive Roman","font_id":"1.000;UKWN;ChunkFive-Roman","font_subfamily":"Roman","font_family":"ChunkFive","font_copyright":null,"font_file":"Chunkfive.otf","font_path":"data/tmp/5714f77d787b1/Chunkfive.otf","font_price":123.45,"check_price":false}],"number_files":1,"collapsed":true},{"media_url":"https://s3-us-west-2.amazonaws.com/aprepara/contents/25uy9ahkjznheptzwxfij73iyz6sswl6.zip","files":[{"font_name":"Cousine Bold","font_id":"Monotype Imaging - Cousine Bold","font_subfamily":"Bold","font_family":"Cousine","font_copyright":null,"font_file":"Cousine-Bold.ttf","font_path":"data/tmp/5714f7885dd42/Cousine-Bold.ttf","font_price":123.45,"check_price":false},{"font_name":"Cousine Bold Italic","font_id":"Monotype Imaging - Cousine Bold Italic","font_subfamily":"Bold Italic","font_family":"Cousine","font_copyright":null,"font_file":"Cousine-BoldItalic.ttf","font_path":"data/tmp/5714f7885dd42/Cousine-BoldItalic.ttf","font_price":29,"check_price":true},{"font_name":"Cousine Italic","font_id":"Monotype Imaging - Cousine Italic","font_subfamily":"Italic","font_family":"Cousine","font_copyright":null,"font_file":"Cousine-Italic.ttf","font_path":"data/tmp/5714f7885dd42/Cousine-Italic.ttf","font_price":123.45,"check_price":false},{"font_name":"Cousine","font_id":"Monotype Imaging - Cousine","font_subfamily":"Regular","font_family":"Cousine","font_copyright":null,"font_file":"Cousine-Regular.ttf","font_path":"data/tmp/5714f7885dd42/Cousine-Regular.ttf","font_price":123.45,"check_price":false}],"number_files":4,"collapsed":true},{"media_url":"https://s3-us-west-2.amazonaws.com/aprepara/contents/4nt5qxxdemc7rqldk7mekumlplbh7pn2.zip","files":[{"font_name":"Open Sans Bold","font_id":"Ascender - Open Sans Bold Build 100","font_subfamily":"Bold","font_family":"Open Sans","font_copyright":null,"font_file":"OpenSans-Bold.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-Bold.ttf","font_price":123.45,"check_price":false},{"font_name":"Open Sans Bold Italic","font_id":"Ascender - Open Sans Bold Italic Build 100","font_subfamily":"Bold Italic","font_family":"Open Sans","font_copyright":null,"font_file":"OpenSans-BoldItalic.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-BoldItalic.ttf","font_price":123.45,"check_price":false},{"font_name":"Open Sans Extrabold","font_id":"Ascender - Open Sans Extrabold Build 100","font_subfamily":"Regular","font_family":"Open Sans Extrabold","font_copyright":null,"font_file":"OpenSans-ExtraBold.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-ExtraBold.ttf","font_price":123.45,"check_price":false},{"font_name":"Open Sans Extrabold Italic","font_id":"Ascender - Open Sans Extrabold Italic Build 100","font_subfamily":"Italic","font_family":"Open Sans Extrabold","font_copyright":null,"font_file":"OpenSans-ExtraBoldItalic.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-ExtraBoldItalic.ttf","font_price":123.45,"check_price":false},{"font_name":"Open Sans Italic","font_id":"Ascender - Open Sans Italic Build 100","font_subfamily":"Italic","font_family":"Open Sans","font_copyright":null,"font_file":"OpenSans-Italic.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-Italic.ttf","font_price":10,"check_price":true},{"font_name":"Open Sans Light","font_id":"Ascender - Open Sans Light Build 100","font_subfamily":"Regular","font_family":"Open Sans Light","font_copyright":null,"font_file":"OpenSans-Light.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-Light.ttf","font_price":123.45,"check_price":false},{"font_name":"Open Sans Light Italic","font_id":"Ascender - Open Sans Light Italic Build 100","font_subfamily":"Italic","font_family":"Open Sans Light","font_copyright":null,"font_file":"OpenSans-LightItalic.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-LightItalic.ttf","font_price":123.45,"check_price":false},{"font_name":"Open Sans","font_id":"Ascender - Open Sans Build 100","font_subfamily":"Regular","font_family":"Open Sans","font_copyright":null,"font_file":"OpenSans-Regular.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-Regular.ttf","font_price":20,"check_price":true},{"font_name":"Open Sans Semibold","font_id":"Ascender - Open Sans Semibold Build 100","font_subfamily":"Regular","font_family":"Open Sans Semibold","font_copyright":null,"font_file":"OpenSans-Semibold.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-Semibold.ttf","font_price":123.45,"check_price":false},{"font_name":"Open Sans Semibold Italic","font_id":"Ascender - Open Sans Semibold Italic Build 100","font_subfamily":"Italic","font_family":"Open Sans Semibold","font_copyright":null,"font_file":"OpenSans-SemiboldItalic.ttf","font_path":"data/tmp/5714f78f34e0e/OpenSans-SemiboldItalic.ttf","font_price":123.45,"check_price":false}],"number_files":10,"collapsed":true},{"media_url":"https://s3-us-west-2.amazonaws.com/aprepara/contents/nu5dvrao29m34hs5axgvbb7ankfgnrjh.zip","files":[{"font_name":"DroidSerifThai Bold","font_id":"Ascender - Droid Serif Thai Bold","font_subfamily":"Bold","font_family":"DroidSerifThai","font_copyright":null,"font_file":"DroidSerifThai-Bold.ttf","font_path":"data/tmp/5714f795b71dc/DroidSerifThai-Bold.ttf","font_price":123.45,"check_price":false},{"font_name":"Droid Serif Thai","font_id":"Ascender - Droid Serif Thai","font_subfamily":"Regular","font_family":"Droid Serif Thai","font_copyright":null,"font_file":"DroidSerifThai-Regular.ttf","font_path":"data/tmp/5714f795b71dc/DroidSerifThai-Regular.ttf","font_price":123.45,"check_price":false}],"number_files":2,"collapsed":true}],"licenses":[{"family_id":"31","check_family":true,"check_weight":true,"money_family":50,"money_weight":123.45},{"family_id":"30","check_family":true,"check_weight":false,"money_family":150},{"family_id":"29","check_family":true,"money_family":0}],"family_name":ipsumService.words(5)});
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