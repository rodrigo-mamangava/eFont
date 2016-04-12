ShopApp.controller('LicensesCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.form = {};
	//clean
	$scope.cleanLicenses = function(){
		$scope.licenses = [];
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
			$scope.form = {};
			//$scope.form = {address: "Rua Engenheiro Agronômico Andrei Cristian Ferreira - Carvoeira", address_city: "Florianópolis", address_country: "Brasil", address_postal_code: "88040", address_state: "Santa Catarina", address_sublocality: "Carvoeira", contact: "claudio", email: "contato@bergmannsoft.com.br", map_lat: -27.6017412, map_lng: -48.52187349999997, name: guid(), phone: 489999999, route: "Rua Engenheiro Agronômico Andrei Cristian Ferreira"};
		}
		$timeout(function(){ delete $localStorage.LicensesId; }, 500);
		$timeout(function(){ isSpinnerBar(false);}, 1000);
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
});