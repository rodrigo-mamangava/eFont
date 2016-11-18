ShopApp.controller('LicensesCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	//global
	$scope.searchText = '';	
	$scope.form = {'formats':[], 'basic_licenses': [], 'showCustomLicenseInfosSection' : false };

	$scope.onlyNumbers = /^\d+$/;
	$scope.hideCustom = false;
	$scope.disableBasicActivation = false;

	//clean
	$scope.cleanLicenses = function(){
		$scope.licenses = new Array();
		$scope.totalItems = new Array();
		$scope.currentPage = new Array();
		$scope.radioModel = new Array();
		$scope.maxSize = new Array();
		$scope.selected_items = new Array();
		$scope.selectedAll = new Array();

		$scope.basic_licenses = new Array();
		$scope.custom_basic_licenses = new Array();

		$scope.licenses[0] = [];
		$scope.licenses[1] = [];

		$scope.form = {'formats':[], 'basic_licenses': [], 'showCustomLicenseInfosSection' : false };

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

		$scope.selectedAll[0] = 0;
		$scope.selectedAll[1] = 0;

		$scope.companyData = null;
		$scope.hideCustom = false;
		$scope.disableBasicActivation = false;
		
	};
	$scope.startLicenses = function(){
		$scope.initLicenses('',1);

		//Mandatorio, deve ser executado por ultimo pra garantir a vida da lista
		$timeout(function() {
			$scope.initLicenses('', 0);
		}, 200);
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
						if ( check_custom == '1' ){
							$scope.custom_basic_licenses = res.data.custom_basic_licenses;
						}
					});
					toggleCustomByBasicActivated();
					toggleDisbaleBasicActivationByCustomActivated();
				});
			}else{
				$scope.cleanLicenses();
				$timeout(function(){
					if ( check_custom == 0 ){
						$scope.hideCustom = true;
					}
				},300);
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
					toggleCustomByBasicActivated();
					toggleDisbaleBasicActivationByCustomActivated()
				},2000);
			}else{
				bootbox.alert(res.data);
			}
			$timeout(function(){ isSpinnerBar(false);}, 500);
		});
	};

	//Monta estrutura para forms
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

		$scope.getBasicLicenses( $localStorage.LicensesId, $scope.form.check_custom );

		if(!isBlank($localStorage.LicensesId)){
			isSpinnerBar(true);
			var id = $localStorage.LicensesId;

			ShopSrvc.getLicenses(id).then(function(res){
				if(res.status == true){
					$scope.form = res.data;
					console.log($scope.form);
					if ( $scope.form.check_custom == 0 ){
						if ( typeof $scope.form.formats[0] === 'undefined' ){
							$scope.form.formats[0] = [
								{parameters:null, multiplier:null }
							];
						}
						$timeout(function(){
							$scope.toggleAcceptedFiles( $scope.form.check_fmt_trial );
						}, 500);

					} else {
						if ( typeof $scope.form.basic_licenses !== 'undefined' ) {
							var countEnbaled = 0;
							angular.forEach($scope.form.basic_licenses, function (item, k) {
								if(item.check_enabled === true){
									countEnbaled = countEnbaled + 1;
								}
							});

							if ( countEnbaled > 0 ){
								$scope.form.showCustomLicenseInfosSection = true;
							} else {
								$scope.form.showCustomLicenseInfosSection = false;
							}
						}
					}
				}else{
					bootbox.alert(res.data);
					$scope.form = {};
				}
			});		
		}else{
			if ( $scope.form.check_custom == 0 ){
				$scope.form.formats[0] = [
					{parameters:null, multiplier:null }
				];
			}
			$scope.form.formats = [
					[{parameters:null, multiplier: null, license_basic_id: null }]
				];
			//$scope.form.basic_licenses = [{}];
			$scope.form.showCustomLicenseInfosSection = false;
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
									$scope.licenses[check_custom].splice(k, 1);
								}
							});
							//Garante que nao exibira botoes para cadastrar custom sem existir Basicas
							$timeout(function(){
								if ( $scope.licenses[0].length == 0 ) {
									$scope.hideCustom = true;
								}
							},300);
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
	$scope.addFormatItem = function(id, check_custom, license_basic_id){
		console.log($scope.form.formats);
		//$scope.form.formats_custom[id].push({parameters: ipsumService.words(5), multiplier:getRandomInt() });
		if ( check_custom == 0 ){
			$scope.form.formats[id].push({parameters:null, multiplier:null });
		}

		if ( check_custom == 1 ) {
			$scope.form.formats[id].push({parameters:null, multiplier:null, license_basic_id: license_basic_id });
		}
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
	//Licenca Basicas
	$scope.setBasicLicenseId = function(l_key, l_id){
		if(!isBlank( $scope.form.basic_licenses[l_id] ) || typeof $scope.form.basic_licenses[l_id] !== 'undefined' ) {
			$scope.form.basic_licenses[l_id].license_basic_id = l_id;
		}
	};
	//
	$scope.getBasicLicenses = function ( id, check_custom ) {
		if ( check_custom == 1 ) {
			ShopSrvc.getListLicenses( '', 1000, 0, 0).then(function(res){
				$scope.basic_licenses = res.data.items;
				angular.forEach($scope.basic_licenses, function (item, key) {
					if(isBlank( id ) || typeof id === 'undefined' ) {
						$scope.form.basic_licenses[item.id] = { license_basic_id : item.id, name : item.name, check_enabled: false };

						//elimina array default
						$scope.form.formats.splice(0, 1);

						$scope.form.formats[item.id] = [
							{parameters:null, multiplier:null, license_basic_id: item.id }
						];
					}
				});
			});
		}
	};

	$scope.addLicenseInfoSectionByCheckedLicence = function ( clicked_item ){

		var countLicenseChecked = 0;

		$scope.form.basic_licenses [ clicked_item.id ].name = clicked_item.name;
		$scope.form.basic_licenses [ clicked_item.id ].license_basic_id = clicked_item.id;

		angular.forEach($scope.form.basic_licenses, function (item, key) {
			if(item.check_enabled === true){
				countLicenseChecked = countLicenseChecked + 1;
				//item
			}
		});

		if ( countLicenseChecked > 0 ) {
			$scope.form.showCustomLicenseInfosSection = true;

			$timeout(function(){
				$scope.$apply(function(){
					$scope.form.formats[clicked_item.id] = [
						{parameters:null, multiplier:null, license_basic_id: clicked_item.id }
					];
				});
			},100);

		} else {
			$scope.form.showCustomLicenseInfosSection = false;
		}
	};

	/**
	 * Sobrescreve valor com unico formato aceito(PDF)
	 * @param check_trial
	 */
	$scope.toggleAcceptedFiles = function( check_trial ) {
		var acceptedFiles = "";
		if ( check_trial ==  true ) {
			acceptedFiles = ".pdf";
		} else {
			acceptedFiles = "";
		}
		$timeout(function(){
			$('#accepted_file').val( acceptedFiles ).trigger("change");
		},1000);
	}

	function toggleCustomByBasicActivated() {
		var countEnableds = 0;
		angular.forEach($scope.licenses[0], function (item, k) {
			if(item.check_enabled === true){
				countEnableds = countEnableds + 1;
			}
		});

		if ( countEnableds > 0 ) {
			$scope.hideCustom = true;
		} else {
			$scope.hideCustom = false;
		}
	}

	function toggleDisbaleBasicActivationByCustomActivated() {
		var countEnableds = 0;
		angular.forEach($scope.licenses[1], function (item, k) {
			if(item.check_enabled === true){
				countEnableds = countEnableds + 1;
			}
		});

		if ( countEnableds > 0 ) {
			$scope.disableBasicActivation = true;
		} else {
			$scope.disableBasicActivation = false;
		}
	}
}).filter('removeBlackItems', function() {
	return function(array) {
		var filteredArray = [];
		angular.forEach(array, function(item) {
			if (item) filteredArray.push(item);
		});
		return filteredArray;
	};
});