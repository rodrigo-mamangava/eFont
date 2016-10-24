ShopApp.controller('LicensesCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.form = {'formats':[0,1,2,3,4]};
	$scope.onlyNumbers = /^\d+$/;
	//clean
	$scope.cleanLicenses = function(){
		$scope.licenses = new Array();
		$scope.totalItems = new Array();
		$scope.currentPage = new Array();
		$scope.radioModel = new Array();
		$scope.maxSize = new Array();
		$scope.selected_items = new Array();

		$scope.licenses[0] = [];
		$scope.licenses[1] = [];

		$scope.form = {'formats':[0,1,2,3,4]};
		//Pag
		$scope.totalItems[0] = 0;
		$scope.currentPage[0] = 0;
		$scope.radioModel[0] = '10';
		$scope.maxSize[0] = 10;

		$scope.totalItems[1] = 0;
		$scope.currentPage[1] = 0;
		$scope.radioModel[1] = '10';
		$scope.maxSize[1] = 10;

		//Checkbox
		$scope.selected_items[0] = 0;
		$scope.selected_items[1] = 0;

		$scope.companyData = null;
		
	};
	$scope.startLicenses = function(){
		$scope.initLicenses('',0);
		$scope.initLicenses('',1);
	};

	//Lista de itens
	$scope.initLicenses = function(search, check_custom){//Carrega todos os itens
		search = typeof search !== 'undefined' ? search : $scope.searchText;
		check_custom = typeof check_custom !== 'undefined' ? check_custom : 0;
		isSpinnerBar(true);	

		ShopSrvc.getListLicenses(search, $scope.radioModel[check_custom], $scope.currentPage[check_custom], check_custom).then(function(res){
			if(res.status == true){
				$timeout(function(){
					$scope.$apply(function(){
						$scope.licenses[check_custom] = res.data.items;
						$scope.totalItems[check_custom] = res.data.total;
						$scope.currentPage[check_custom] = res.data.offset;
						$scope.companyData = res.data.company;

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
			}
			$timeout(function(){ isSpinnerBar(false);}, 500);
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

	$scope.editCustomLicenses = function(id){
		isSpinnerBar(true);
		$localStorage.LicensesId = id;
		$timeout(function(){
			$scope.changeTemplateURL('/ef-licenses/custom-form');
		}, 500);
	};

	//Ativacao
	$scope.activateLicense = function(item){
		isSpinnerBar(true);
		ShopSrvc.activateLicense(item).then(function(res){
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
	
	$scope.getLicenses = function(){
		//Get company profile
		ShopSrvc.getCompanyProfile().then(function(res){
			if(res.status == true){
				$scope.companyData = res.data.company;
			}else{
				bootbox.alert(res.data);
				$scope.form = {};
			}
		});

		if(!isBlank($localStorage.LicensesId)){
			isSpinnerBar(true);
			var id = $localStorage.LicensesId;

			ShopSrvc.getLicenses(id).then(function(res){
				if(res.status == true){
					$scope.form = res.data;
					console.log("$scope.form: " + $scope.form);
				}else{
					bootbox.alert(res.data);
					$scope.form = {};
				}
			});		
		}else{
			//$scope.form = {'formats':[[{parameters: ipsumService.words(5), multiplier:getRandomInt() }],[{parameters: ipsumService.words(5), multiplier:getRandomInt() }],[{parameters: ipsumService.words(5), multiplier:getRandomInt() }],[{parameters: ipsumService.words(5), multiplier:getRandomInt() }]]};
			$scope.form = {
				'formats':[
					[{parameters:null, multiplier: null }],
					[{parameters:null, multiplier: null }],
					[{parameters:null, multiplier: null }],
					[{parameters:null, multiplier: null }],
					[{parameters:null, multiplier: null }]
				]
			};
		}
		$timeout(function(){ delete $localStorage.LicensesId; }, 500);
		$timeout(function(){ 
			isSpinnerBar(false);
			isDropzone();
		}, 1000);
	};
	//Remover varios itens
	$scope.removeSelected = function(y, n, t, m, err, check_custom) {
		var html = '';
		if($scope.selected_items[check_custom] < 1){
			bootbox.alert(err);
		}else{
			angular.forEach($scope.licenses[check_custom], function (item, k) {
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
							angular.forEach($scope.licenses[check_custom], function (item, k) {
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
		//$scope.form.formats_custom[id].push({parameters: ipsumService.words(5), multiplier:getRandomInt() });
		$scope.form.formats[id].push({parameters:null, multiplier:null });
		console.log(id);
	};
	//Remove um Formato
	$scope.removeFormatItem = function(id, cart_key){
		console.log(cart_key, id);
		$scope.form.formats[id].splice(cart_key, 1);
	};

	//Alterando paginacao
	$scope.pageChanged = function (check_custom){ $scope.initLicenses('',check_custom); }
	//Select all
	$scope.checkAll = function (check_custom) {
		if ($scope.selectedAll[check_custom]) {
			$scope.selectedAll[check_custom] = true;
			$scope.selected_items[check_custom] = $scope.totalItems[check_custom];
		} else {
			$scope.selectedAll[check_custom] = false;
			$scope.selected_items[check_custom] = 0;
		}
		angular.forEach($scope.licenses[check_custom], function (item) {
			item.Selected = $scope.selectedAll[check_custom];
		});

	};
	//Se selecionado
	$scope.isSelected = function(id, check_custom) {
		if ($('#'+id).is(':checked')) {
			$scope.selected_items[check_custom]++;
		}else{
			$scope.selected_items[check_custom]--;
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