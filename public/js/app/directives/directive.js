/**
 * Troca de View/Template
 */
ShopApp.directive("dynamicTemplate",function($localStorage, $timeout){
	return {
		restrict: 'A',
		replace: true,
		template: '<div ng-include="template_url"></div>',
		controller: function($scope){
			$scope.changeTemplateURL = function(where, search, tab, nav){
				$scope.template_url = where;
				search = typeof search !== 'undefined' ? search : null;
				$localStorage.search = search;
				$localStorage.enterDynamicTemplate = {'where':where, 'search': search, 'tab': tab, 'nav':nav};

				tab = typeof tab !== 'undefined' ? tab : null;	
				if(tab != null && tab.length > 1){
					$('.nav li').removeClass('c-active');
					$('#'+tab).addClass('c-active');
				}	

				nav = typeof nav !== 'undefined' ? nav : null;
				if(nav != null && nav.length > 1){
					$timeout(function(){
						$('.c-dropdown-menu li').removeClass('c-active');
						$('#'+nav).addClass('c-active');
					},200);
				}
				
				console.log(where, search, tab, nav);
			}
		}
	};
});

/**
 * Convertendo string para numero
 */
ShopApp.directive('stringToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
    	try{
    		ngModel.$parsers.push(function(value) {
    			return '' + value;
    		});
    		ngModel.$formatters.push(function(value) {
    			return parseFloat(value, 10);
    		});
    	}catch(err){
    		return '' + value; 
    	}
    }
  };
});
/**
 * Imagem de funto
 */
ShopApp.directive('backgroundImageDirective', function () {
    return function (scope, element, attrs) {
        element.css({
            'background-image': 'url(' + attrs.backgroundImageDirective + ')',
            'background-repeat': 'no-repeat',
        });
    };
});
/**
 * URL Encode
 */
ShopApp.filter('escape', function() {
    return function(input) {
        if(input) {
            return window.encodeURIComponent(input); 
        }
        return "";
    }
});
/**
 * Resolvendo problemas de barras duplas
 */
ShopApp.filter('stripslashes', function () {
    return function (data) {
    	if(data != null && data.length > 1){
    		return stripslashes(data);
    	}else{
    		return (data != 'null')?data:'';
    	}
    };
});
/**
 * Conversao de moeda
 */
ShopApp.filter('comma2decimal', function () {
	return function(input) {
		return input.toLocaleString('de-DE');
	};
});
/**
 * Imagem de profile
 */
ShopApp.filter('userimage', function () {
    return function (data) {
    	if(data != null && data.length > 1){
    		return data;
    	}else{
    		return '/img/bolaozao-192x192.png';
    	}
    };
});
/**
 * Formatando data para padrao brasil
 */
ShopApp.filter('dateFormat', function($filter){
	return function(input){
		if(input == null){ 
			return ""; 
		} 
		var _date = $filter('date')(new Date(input), 'dd/MM/yyyy HH:mm:ss');
		return _date.toUpperCase();
	};
});