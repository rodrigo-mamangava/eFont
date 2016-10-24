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
		 * Shopping
		 */
		getListProductList: function(search, count, offset){
			//GET
			return $http.get('/shop-product-list/search?search='+search+'&count='+count+'&offset='+offset)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		/**
		 * Details
		 */
		getProductDetails : function(id){
			//GET
			return $http.get('/shop-product-details/edit?id='+id)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});				
		},
		/**
		 * Formatos
		 */
		getListFormats: function(){
			//GET
			return $http.get('/ef-formats/search')
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});				
		},
		/**
		 * Font Files
		 */
		getListFontFiles: function(data){
			//GET
			return $http.post('/ef-font-files/uncompress', data)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});				
		},		
		/**
		 * Licencas
		 */
		saveLicenses: function(data){
			//POST
			return $http.post('/ef-licenses/save', data)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		getListActiveLicenses: function(){
			//GET
			return $http.get('/ef-licenses/active')
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});				
		},
		getListLicenses: function(search, count, offset, check_custom){
			//GET
			return $http.get('/ef-licenses/search?search='+search+'&count='+count+'&offset='+offset+'&check_custom='+check_custom)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		getLicenses: function(id){
			//GET
			return $http.get('/ef-licenses/edit?id='+id)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},	
		removeLicenses: function(id){
			//GET
			return $http.get('/ef-licenses/remove?id='+id)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		activateLicense: function(data){
			//POST
			return $http.post('/ef-licenses/activate', data)
				.then(function(res) {
					return res.data;
				},function (err) {
					console.log(err);
				});
		},
		/**
		 * Produtos
		 */
		saveProducts: function(data){
			//POST
			return $http.post('/ef-products/save', data)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		getListProducts: function(search, count, offset){
			//GET
			return $http.get('/ef-products/search?search='+search+'&count='+count+'&offset='+offset)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},
		getProducts: function(id){
			//GET
			return $http.get('/ef-products/edit?id='+id)
			.then(function(res) {
				return res.data;
			},function (err) {
				console.log(err);
			});	
		},	
		removeProducts: function(id){
			//GET
			return $http.get('/ef-products/remove?id='+id)
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
		 * Company Profile
		 */
		saveCompanyProfile: function(data){
			//POST
			return $http.post('/ef-company-profile/save', data)
				.then(function(res) {
					return res.data;
				},function (err) {
					console.log(err);
				});
		},
		getCompanyProfile: function(){
			//GET
			return $http.get('/ef-company-profile/search')
				.then(function(res) {
					return res.data;
				},function (err) {
					console.log(err);
				});
		},
		/**
		 * Checkout
		 */
		goCheckoutCompleted : function(data){
			//POST
			return $http.post('/shop-checkout/checkout', data)
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