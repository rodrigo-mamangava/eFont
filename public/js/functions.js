/**
 * For checking if a string is blank, null or undefined I use:
 * @param str
 * @returns
 */
function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}
/**
 * Verifica se um elemento esta vazio
 * @param str
 * @returns {Boolean}
 */
function isEmpty(str) {
    return (!str || 0 === str.length);
}
/**
 *  sleep function in js
 * @param milliseconds
 */
function sleep(milliseconds) {
	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds){
			break;
		}
	}
}

/**
 * Exibe ou oculta o spinner bar
 * @param load
 */
function isSpinnerBar(load){
	if(load === true){
		$('#page-spinner-bar').removeClass('ng-hide hide').addClass('ng-show show');
		$('#no-data-to-display').removeClass('show').addClass('hide');
	}else{
		$('#page-spinner-bar').removeClass('ng-show show').addClass('ng-hide hide');
		$('#no-data-to-display').removeClass('hide').addClass('show');
	}
}
/**
 * Ativando switch button via javascripts
 */
function isMakeSwitch(){
	$(".make-switch").bootstrapSwitch();
}
/**
 * Carregando select2me quando chamado via ajax
 */
function isSelect2me(){
	$('.select2me').select2({ allowClear: false});
}
/**
 * Inicializa os scripts
 */
function isApp(){
	App.init(); // init core    
}
/**
 * Inicializa o slider pages
 */
function isPageScripts(){
	var slider = $('.c-layout-revo-slider .tp-banner');
	var cont = $('.c-layout-revo-slider .tp-banner-container');
	var api = slider.show().revolution({
		sliderType: "standard",
		sliderLayout: "fullscreen",
		responsiveLevels: [2048, 1024, 778, 320],
		gridwidth: [1240, 1024, 778, 320],
		gridheight: [868, 768, 960, 720],
		delay: 15000,
		startwidth: 1170,
		startheight: App.getViewPort().height,
		navigationType: "hide",
		navigationArrows: "solo",
		touchenabled: "on",
		navigation:
		{
			keyboardNavigation: "off",
			keyboard_direction: "horizontal",
			mouseScrollNavigation: "off",
			onHoverStop: "on",
			arrows:
			{
				style: "circle",
				enable: true,
				hide_onmobile: false,
				hide_onleave: false,
				tmp: '',
				left:
				{
					h_align: "left",
					v_align: "center",
					h_offset: 30,
					v_offset: 0
				},
				right:
				{
					h_align: "right",
					v_align: "center",
					h_offset: 30,
					v_offset: 0
				}
			}
		},
		spinner: "spinner2",
		fullScreenOffsetContainer: '.c-layout-header',
		shadow: 0,
		disableProgressBar: "on",
		hideThumbsOnMobile: "on",
		hideNavDelayOnMobile: 1500,
		hideBulletsOnMobile: "on",
		hideArrowsOnMobile: "on",
		hideThumbsUnderResolution: 0
	});
}

/**
 * Seta um valor do select2
 * @param key
 * @param value
 */
function setSelect2me(key, value, simple){
	console.log(key, value);
	simple = typeof simple !== 'undefined' ?  simple : true;
	
	if(simple == true){
		$("#"+key).val(value).trigger("change");
		console.log(1);
	}else{
		console.log(2);
		$("#"+key).select2().select2("val", value);
	}
}
/**
 * Carregamento dinamico
 * @param elm
 * @param url
 */
function isSelect2RemoteData(elm, url, help){
	try{
		if ( $( "#"+elm ).length ) {
			$("#"+elm).select2({
				allowClear: true,
				triggerChange: true,
				ajax: {
					url: url,
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return { search: params.term,  count: 10 };
					},
					processResults: function (data, params) {
						params.page = params.page || 1;
						if(data.status == true){
							var res = []; var items = data.data.items;
							for(var i = 0; i < items.length; i++){ res.push({id: items[i].id, text: stripslashes(items[i].name) } ); }
							return { results: res};
						}else{
							return {results: false};
						}
					},
					cache: true
				},
				escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
				minimumInputLength: 0,
			}).select2('val', []);	

			help = typeof help !== 'undefined' ? help : false;
			if(help !== false){
				$("#"+elm).change(function() {
					setValue(help, $("#"+elm).text());
				});
			}

		}
	}catch(err){
		console.log(err);
	}
	
}
/**
 * Seta um valor
 * @param key
 * @param value
 */
function setValue(key, value){
	$('#'+key).empty();
	$('#'+key).html(value);
}
/**
 * Selecionando um item
 * @param index
 * @param id
 */
function isSelectedPlatform(id){
	$('#platform').val(id).trigger("change");
}
/**
 * Ativa o icheck via javascript
 */
function isICheck(changed){
	changed = typeof changed !== 'undefined' ?  changed : false;

	$(document).ready(function(){
		$('.icheck').iCheck({
			checkboxClass: 'icheckbox_square-yellow',
			radioClass: 'iradio_square-yellow',
			increaseArea: '20%' // optional
		});
		
		if(changed === true){
			$('.icheck').on('ifChecked', function(event){
				$('#'+event.target.name).val(true).trigger('change');
			});

			$('.icheck').on('ifUnchecked', function(event){
				$('#'+event.target.name).val(false).trigger('change');
			});	
		}
	});
}

/**
 * Forca o calendario via class
 */
function isDateTimepicker (){
	$.fn.datetimepicker.dates['pt-BR'] = {
	        format: 'dd/mm/yyyy',
			days: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado", "Domingo"],
			daysShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb", "Dom"],
			daysMin: ["Do", "Se", "Te", "Qu", "Qu", "Se", "Sa", "Do"],
			months: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
			monthsShort: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
			today: "Hoje",
			suffix: [],
			meridiem: []
		};
	
	$(".form_datetime").datetimepicker({
		autoclose: true,
		format: "dd/mm/yyyy hh:ii",
		maxDate: new Date(), 
		pickerPosition: "bottom-right",
		language: 'pt-BR'
	});
}
/**
 * Ativando o popover via jquery
 */
function isPopover(){
	$('[data-toggle="popover"]').popover({
		placement : 'top',
		trigger : 'hover'
	});
}
/**
 * Ativando markdown editor por jquery
 */
function isMarkdown(){
	$('[data-provide="markdown"]').markdown({});	
}
/**
 * Ativando dropzone por jquery
 */
function isDropzone(){
	FormDropzone.init();
	//$("#my-dropzone").dropzone({ url: "/file/post" });
}
/**
 * Carrega um arquivo e retorna para o input informado
 * @param id
 */
function fileUploadDropzone(id, uuid){
	uuid = typeof uuid !== 'undefined' ?  uuid : false;
	
	$('#dropzone-to-imagem').val('');
	$('#dropzone-to-id').val(id);
	$('#uuid-to-id').val(uuid);
	$('#modal_file_upload_form_static').modal('show');
}
/**
 * Tipo da extensao de um arquivo
 * @param filename
 * @returns
 */
function isExtesionFile(filename){
	return (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename) : false;
}
/**
 * escape slashes to/from a string
 * @param str
 * @returns
 */
function addslashes(str) {
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\'/g, '\\\'');
    str = str.replace(/\"/g, '\\"');
    str = str.replace(/\0/g, '\\0');
    return str;
}
 /**
  * escape slashes to/from a string
  * @param str
  * @returns
  */
function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

/**
 * Setando imagem de fundo
 */
function isBackgroundPhoto(id, base64){
	$('#'+id).css("background-image", "url('"+base64+"')");
	$('#'+id).css("background-size", "100% 100%");
	$('#'+id).css("background-size", "100% 100%");	
}

/**
 * Se a url eh valida
 * @param str
 * @returns {Boolean}
 */
function isValidURL(str) {
	var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
			'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|'+ // domain name
			'((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
			'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
			'(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
			'(\\#[-a-z\\d_]*)?$','i'); // fragment locator
	return pattern.test(str);
}

/**
 * Create GUID / UUID in JavaScript
 * @returns {String}
 */
function guid() {
	function s4() {
		return Math.floor((1 + Math.random()) * 0x10000)
		.toString(16)
		.substring(1);
	}
	return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
	s4() + '-' + s4() + s4() + s4();
}

/**
 * Returns a random number between min (inclusive) and max (exclusive)
 * https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Objects/Math/random
 */
function getRandomArbitrary(min, max) {
	return Math.random() * (max - min) + min;
}

/**
 * Returns a random integer between min (inclusive) and max (inclusive)
 * Using Math.round() will give you a non-uniform distribution!
 * https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Objects/Math/random
 */
function getRandomInt(min, max) {
	var min = typeof min !== 'undefined' ? min : 0;
	var max = typeof max !== 'undefined' ? max : 100;

	return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Returns a random number between 0 (inclusive) and 1 (exclusive)
 * https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Objects/Math/random  
 */
function getRandom() {
  return Math.random();
}