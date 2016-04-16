var ShopApp = angular.module('ShopApp', ['ngStorage', 'ngSanitize','ngFileUpload' ,'ui.bootstrap', 'ui.select2','ui.sortable', 'ui.utils.masks', 'frapontillo.bootstrap-switch', 'ipsum']);

/* Run */
ShopApp.run(function($rootScope, $timeout) {
	//Loading
	$rootScope.$on('$stateChangeStart',function(){
		isSpinnerBar(true);
	});
	$rootScope.$on('$stateChangeSuccess',function(){
		isSpinnerBar(false);
	});	
});