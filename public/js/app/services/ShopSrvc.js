ShopApp.factory('ShopSrvc', function($q, $timeout, $http, $localStorage, ipsumService) {
	var deferred = $q.defer();
	var debug = true;
	return {
		/**
		 * Register/Login
		 */
		login: function(data){
			//GET
			return $http.post('/login', data)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		register: function(data){
			//GET
			return $http.post('/sign-up', data)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},		
		/**
		 * Profile
		 */
		saveProfile: function(data){
			//POST
			return $http.post('/ef-profile/save', data)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		getProfile: function(){
			//GET
			return $http.get('/ef-profile/edit')
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},			
		/**
		 * Fake Random
		 */
		fake: function($scope){
			var person = ipsumService.randomName('r');
			var email =  person.first + '.' + person.last + '@' + ipsumService.words(1) + ipsumService.randomItem(['.net','.org','.com','.biz']);
			var username = person.first + '.' + person.last;
			
			$timeout(function(){
				$scope.$apply(function(){
					$scope.form = {
							username: email, 
							password: 'abcd1234', 
							provider: 'generic',
							name: ipsumService.words(5),
							fullname: person.first + '.' + person.last,
							firstname: person.first,
							lastname: person.last,
							address: ipsumService.words(10),
							complement: ipsumService.words(3),
							city: ipsumService.words(2),
							country: ipsumService.words(1),
							email: email};			

				});
			},500);
		},
		/**
		 * Debug
		 */
		isDebug: function(){
			if(debug === true){
				console.log(' ---------- Debug Enable -----------');
			}
			return debug;
		}
	}
});	