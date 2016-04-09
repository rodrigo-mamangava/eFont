ShopApp.controller('RegisterCtrl', function($scope, $timeout, $http, $localStorage, ipsumService, ShopSrvc) {
	$scope.form = {
			username: '', 
			password: '', 
			provider: 'generic'};
	$scope.buttons = true;
	/**
	 * Login
	 */
	$scope.login = function(){
		isSpinnerBar(true);	
		$scope.buttons = false;

		ShopSrvc.login($scope.form).then(function(res){
			if(res.status == true){
				window.location.href = res.data;
			}else{
				bootbox.alert(res.data);
				$scope.buttons = true;
			}
			$timeout(function(){
				isSpinnerBar(false);				
			},1000);
		}, function(err){
			isSpinnerBar(false);
			$scope.buttons = true;
		});
	};	
	/**
	 * Registro
	 */
	$scope.register = function(){
		isSpinnerBar(true);	
		$scope.buttons = false;

		ShopSrvc.register($scope.form).then(function(res){
			if(res.status == true){
				window.location.href = res.data;
			}else{
				bootbox.alert(res.data);
			}
			$timeout(function(){
				isSpinnerBar(false);				
			},1000);
		}, function(err){
			isSpinnerBar(false);
		});
	};	
	/**
	 * Outras funcoes
	 */
	$scope.notimplemented = function(){
		bootbox.alert('Not implemented.');
	};
	
	$timeout(function(){
		isApp();
		isSpinnerBar(false);	
	},100);
	
	/**
	 * Dev mode
	 */
	if(ShopSrvc.isDebug() === true){
		ShopSrvc.fake($scope);
	}
});