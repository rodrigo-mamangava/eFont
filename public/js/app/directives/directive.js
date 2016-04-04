ShopApp.directive("dynamicTemplate",function($localStorage){
	return {
		restrict: 'A',
		replace: true,
		template: '<div ng-include="template_url"></div>',
		controller: function($scope){
			$scope.changeTemplateURL = function(where, search, tab){
				$scope.template_url = where;
				console.log(where, search, tab);
				
				search = typeof search !== 'undefined' ? search : null;
				$localStorage.search = search;
				
				tab = typeof tab !== 'undefined' ? tab : null;	
				if(tab != null && tab.length > 1){
					$('.nav li').removeClass('active open selected');
					$('#'+tab).addClass('active open selected');
				}				
			}
		}
	};
});


ShopApp.directive('stringToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      ngModel.$parsers.push(function(value) {
        return '' + value;
      });
      ngModel.$formatters.push(function(value) {
        return parseFloat(value, 10);
      });
    }
  };
});



ShopApp.filter('stripslashes', function () {
    return function (data) {
    	if(data != null && data.length > 1){
    		return stripslashes(data);
    	}else{
    		return (data != 'null')?data:'';
    	}
    };
});



ShopApp.filter('comma2decimal', function () {
	return function(input) {
		return input.toLocaleString('de-DE');
	};
});


ShopApp.filter('userimage', function () {
    return function (data) {
    	if(data != null && data.length > 1){
    		return data;
    	}else{
    		return '/img/bolaozao-192x192.png';
    	}
    };
});

ShopApp.filter('dateFormat', function($filter){
	return function(input){
		if(input == null){ 
			return ""; 
		} 
		var _date = $filter('date')(new Date(input), 'dd/MM/yyyy HH:mm:ss');
		return _date.toUpperCase();
	};
});