ShopApp.controller('ShopProductDetailsCtrl', function ($scope, $timeout, $http, $localStorage, ShopSrvc) {
    //global
    $scope.searchText = '';



    //clean
    $scope.cleanProductDetails = function () {
        $scope.form = {};
        $scope.products = [];
        $scope.licenses = [];
        $scope.formats = [];
        $scope.families = [];
        $scope.styles = [];
        $scope.collections = [];
        $scope.fonts = [];
        $scope.collection = false;//flag para saber se as collections foram selecionada
        $scope.license = 0;
        $scope.format = '';
        $scope.current = 0;
        $scope.cart = 0.00;
        $scope.selected_numbers = [];

//        $scope.fontSize = '25';
//        $scope.letterSpacing = '1';
//        $scope.fontStyle = 'normal';
//        $scope.fontVariant = 'normal';
//        $scope.fontWeight = 'normal';
//        $scope.textAlign = 'center';
//        $scope.tipo = 'true';



    };
    //Obtem o item
    $scope.getProductDetails = function () {
        if (!isBlank($localStorage.ProductDetailsId)) {
            isSpinnerBar(true);
            var id = $localStorage.ProductDetailsId;



            ShopSrvc.getProductDetails(id).then(function (res) {
                if (res.status == true) {

                    $timeout(function () {
                        $scope.$apply(function () {
                            $scope.form = res.data.project;
                            $scope.license = $scope.form.license;
                            $scope.format = $scope.form.format;

                            $scope.font_src = res.data.font_src;

                            $scope.licenses = res.data.licenses;
                            $scope.formats = res.data.formats;
                            $scope.families = res.data.families;
                            $scope.collections = res.data.collections;
                            $scope.styles = res.data.styles;
                            $scope.fonts = res.data.fonts;

//                            console.log('teste');
//                            console.log($scope.font_src);
//                            console.log('teste');

                            //console.log(res.data);
                            $timeout(function () {
                                isSpinnerBar(false);
                                $scope.pricebook();
                            }, 500);
                        });
                    });
                } else {
                    bootbox.alert(res.data);
                    $scope.form = {};
                    $timeout(function () {
                        isSpinnerBar(false);
                    }, 500);
                }
            });
        } else {
            $scope.form = {};
        }
        $timeout(function () {
            delete $localStorage.ProductDetailsId;
        }, 500);
    };

    $scope.initDetail = function () {
        $scope.fontSize = '25';
        $scope.letterSpacing = '1';
        $scope.fontStyle = 'normal';
        $scope.fontVariant = 'normal';
        $scope.fontWeight = 'normal';
        $scope.textAlign = 'center';
        $scope.tipo = 'true';

    };


    /**
     * Licencas
     */
    $scope.onChangeLicense = function (lu_key, lu_id) {
        $scope.license = lu_key;
        $scope.current = lu_id;
        //console.log(lu_key, lu_id);
        //Recalculando precos
        $scope.pricebook();
    };
    /**
     * Formatos/Multiplicadores
     */
    $scope.onChangeLicenseFormats = function (ll_key, ll_id, ft_id) {
        //console.log(ll_key, ll_id, ft_id);
        //console.log($scope.format[ll_key][ft_id]);
        $timeout(function () {
            $scope.pricebook();
        }, 50);
    };
    /**
     * Seleciona uma collection
     */
    $scope.onChangeSelectedCollection = function (f_key, f_id) {
        //console.log(f_key, f_id);
        $scope.uncheckingAllStyles(f_key);
        $timeout(function () {
            $scope.pricebook();
        }, 10);
    };
    //Collection
    $scope.uncheckingAllCollection = function () {
        angular.forEach($scope.families, function (f, k) {
            f.check_collection = false;
        });
    };
    /**
     * Styles
     */
    $scope.onChangeSelectedItem = function (f_s_key, f_key) {
        //console.log(f_s_key, f_key);
        //Verificando collection
        if ($scope.collection == true) {
            $scope.price = 0;
            $scope.collection = false;
            $scope.uncheckingAllCollection();
        }
        //Calculando pesos
        var number = $scope.selected_numbers[f_key] || 0;
        var price = $scope.cart;

        if ($scope.styles[f_key][f_s_key].selected) {
            $scope.selected_numbers[f_key] = number + 1;
            $scope.pricebook();
        } else {
            $scope.selected_numbers[f_key] = number - 1;
            $scope.pricebook();
        }
    };
    //Styles
    $scope.uncheckingAllStyles = function (f_key) {
        angular.forEach($scope.styles[f_key], function (s, k) {
            $scope.styles[f_key][k].selected = false;
        });
        $scope.selected_numbers[f_key] = 0;
    };
    /**
     * PRICEBOOK
     */
    $scope.pricebook = function () {
        $scope.collection = false;
        $scope.form.collection = 0;
        $scope.cart = 0;

        angular.forEach($scope.families, function (f_value, f_key) {
            $scope.families[f_key].collection = 0;
            $scope.families[f_key].subprice = 0;

            angular.forEach($scope.styles[f_key], function (fs_value, fs_key) {
                $scope.styles[f_key][fs_key].price = 0;
            });
        });
        /**
         * Formatos
         */
        angular.forEach($scope.format[$scope.license], function (ft_value, ft_key) {
            /**
             * Collections
             */
            var price = $scope.collections[$scope.license][ft_key][ft_value];
            angular.forEach(price, function (f_value, f_key) {
                $scope.families[f_key].collection = parseFloat(f_value) + parseFloat($scope.families[f_key].collection);


            });
            /**
             * Styles
             */
            //console.log(ft_key, ft_value);
            if (typeof $scope.fonts[$scope.license][ft_key] !== 'undefined') {
                var styles = $scope.fonts[$scope.license][ft_key][ft_value];
                //console.log(styles);
                angular.forEach(styles, function (f_s_value, f_s_key) {
                    //console.log(f_s_value, f_s_key);
                    angular.forEach(f_s_value, function (f_f_value, f_f_key) {
                        //console.log(f_f_value, f_f_key);
                        angular.forEach(f_f_value, function (f_fs_value, f_fs_key) {
                            //console.log(f_fs_value, f_fs_key, f_s_key);
                            $scope.styles[f_s_key][f_f_key].price = parseFloat(f_fs_value) + parseFloat($scope.styles[f_s_key][f_f_key].price);
                        });
                    });
                });
            }
        });
        /**
         * subprice
         */
        angular.forEach($scope.styles, function (s_item, s_key) {
            angular.forEach(s_item, function (f_fs_value, f_fs_key) {
                //console.log(f_fs_value, f_fs_key);
                if (f_fs_value.selected == true) {
                    $scope.families[s_key].subprice = parseFloat($scope.families[s_key].subprice) + parseFloat(f_fs_value.price);
                }
            });
        });
        /**
         * Familias
         */
        angular.forEach($scope.families, function (f_value, f_key) {
            if ($scope.families[f_key].check_collection == true) {
                $scope.form.collection = parseFloat($scope.families[f_key].collection) + parseFloat($scope.form.collection);
                $scope.collection = true;
            }
        });
        /**
         * Cart
         */
        angular.forEach($scope.families, function (f_item) {
            $scope.cart = parseFloat($scope.cart) + parseFloat(f_item.subprice);
        });
        $scope.cart = parseFloat($scope.cart) + parseFloat($scope.form.collection);
    };
    /**
     * Default
     */
    $scope.notimplemented = function () {
        bootbox.alert('Not implemented.');
    };
    /**
     * Add to cart
     */
    $scope.AddToCart = function () {
        isSpinnerBar(true);
        var your_cart = {form: $scope.form,
            products: $scope.products,
            licenses: $scope.licenses,
            formats: $scope.formats,
            families: $scope.families,
            styles: $scope.styles,
            collections: $scope.collections,
            fonts: $scope.fonts,
            collection: $scope.collection,
            license: $scope.license,
            format: $scope.format,
            current: $scope.current,
            cart: $scope.cart,
            selected_numbers: $scope.selected_numbers};
        $timeout(function () {
            if (!isBlank($localStorage.ShopYourCart)) {
                $localStorage.ShopYourCart.push(your_cart);
                $scope.changeTemplateURL('/shop-cart');
            } else {
                $localStorage.ShopYourCart = [your_cart];
                $scope.changeTemplateURL('/shop-cart');
            }
        }, 20);
    };
    //Init
    $scope.cleanProductDetails();
});