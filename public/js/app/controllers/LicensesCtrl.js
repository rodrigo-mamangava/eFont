ShopApp.controller('LicensesCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.form = {'formats':[0,1,2,3]};
	//clean
	$scope.cleanLicenses = function(){
		$scope.licenses = [];
		$scope.form = {'formats':[0,1,2,3]};
		//Pag
		$scope.totalItems = 0;
		$scope.currentPage = 0;	
		$scope.radioModel = '10';
		$scope.maxSize = 10;	
		//Checkbox
		$scope.selected_items = 0;
	};
	//Lista de itens
	$scope.initLicenses = function(search){//Carrega todos os itens
		search = typeof search !== 'undefined' ? search : $scope.searchText;
		isSpinnerBar(true);	

		ShopSrvc.getListLicenses(search, $scope.radioModel, $scope.currentPage).then(function(res){
			if(res.status == true){
				$timeout(function(){
					$scope.$apply(function(){
						$scope.licenses = res.data.items;
						$scope.totalItems = res.data.total;
						$scope.currentPage = res.data.offset;
					});
				});
			}else{
				$scope.cleanLicenses();
			}
			
			$timeout(function(){ isSpinnerBar(false);}, 500);
		});
	};	
	//Salvando
	$scope.saveLicenses = function (){
		isSpinnerBar(true);	
		ShopSrvc.saveLicenses($scope.form).then(function(res){
			if(res.status == true){
				$scope.changeTemplateURL('/ef-licenses');
			}else{
				bootbox.alert(res.data);
				$timeout(function(){ isSpinnerBar(false);}, 500);
			}
		});
	};
	//Edicao
	$scope.editLicenses = function(id){
		isSpinnerBar(true);	
		$localStorage.LicensesId = id;
		$timeout(function(){
			$scope.changeTemplateURL('/ef-licenses/form');
		}, 500);
	};
	
	$scope.getLicenses = function(){
		if(!isBlank($localStorage.LicensesId)){
			isSpinnerBar(true);	
			var id = $localStorage.LicensesId;
			
			ShopSrvc.getLicenses(id).then(function(res){
				if(res.status == true){
					$scope.form = res.data;
				}else{
					bootbox.alert(res.data);
					$scope.form = {};
				}
			});		
		}else{
			$scope.form = {'formats':[[{parameters: ipsumService.words(5), multiplier:getRandomInt() }],[{parameters: ipsumService.words(5), multiplier:getRandomInt() }],[{parameters: ipsumService.words(5), multiplier:getRandomInt() }],[{parameters: ipsumService.words(5), multiplier:getRandomInt() }]]};
		}
		$timeout(function(){ delete $localStorage.LicensesId; }, 500);
		$timeout(function(){ 
			isSpinnerBar(false);
			isDropzone();
		}, 1000);
	};
	//Remover varios itens
	$scope.removeSelected = function(y, n, t, m, err) {
		var html = '';
		if($scope.selected_items < 1){
			bootbox.alert(err);
		}else{
			angular.forEach($scope.licenses, function (item, k) {
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
							angular.forEach($scope.licenses, function (item, k) {
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
		ShopSrvc.removeLicenses(id).then(function(res){
			if(res.status == true){
				$('#licenses-tr-'+id).addClass('hide');
				isSpinnerBar(false);
			}else{
				$('#licenses-tr-'+id).addClass('danger');
				isSpinnerBar(false);
			}									
		});
	};	
	//Add Formatos
	$scope.addFormatItem = function(id){
		console.log($scope.form.formats);
		$scope.form.formats[id].push({parameters: ipsumService.words(5), multiplier:getRandomInt() });
		console.log(id);
	};
	//Remove uma Formato
	$scope.removeFormatItem = function(id, cart_key){
		console.log(cart_key, id);
		$scope.form.formats[id].splice(cart_key, 1);
	};
	//Alterando paginacao
	$scope.pageChanged = function (){ $scope.initLicenses(); }
	//Select all
	$scope.checkAll = function () {
		if ($scope.selectedAll) {
			$scope.selectedAll = true;
			$scope.selected_items = $scope.totalItems;
		} else {
			$scope.selectedAll = false;
			$scope.selected_items = 0;
		}
		angular.forEach($scope.licenses, function (item) {
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
	$scope.cleanLicenses();
	$('#qz-query').keypress(function(e) {
		if(e.which == 13) {
			$scope.initLicenses($('#qz-query').val());
		}
	});	
	/**
	 * Default
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
	
	//Sortable
	$scope.sortableFormatDesktop = {
			update: function(e, ui) {
				//console.log($scope.formats[1]);
			},
			stop: function(e, ui) {
				//console.log($scope.formats[1]);
			}
	};	
});