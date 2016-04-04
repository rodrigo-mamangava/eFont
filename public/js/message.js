function showSuccessOrDanger(status, msg){
	//Oculta se tiver algum em aberto
	hideAllAlerts();
	//Exibe de acordo com o status
	if(status == true){
		$('#alert-succes').addClass('show').removeClass('hide');
		$('#alert-succes').html(msg);
	}else{
		$('#alert-message').addClass('show').removeClass('hide');
		$('#alert-message').html(msg);
	}		
	isSpinnerBar(false);
}


function showAlertWarning(msg){
	hideAllAlerts();
	$('#alert-msg-warning').html(msg);
	
	isSpinnerBar(false);
}

function showAlertInfo(msg){
	hideAllAlerts();
	$('#alert-msg-info').html(msg);

	isSpinnerBar(false);
}


function hideAllAlerts(){
	$('#alert-succes').addClass('hide').removeClass('show');
	$('#alert--info').addClass('hide').removeClass('show');
	$('#alert--warning').addClass('hide').removeClass('show');
	$('#alert-message').addClass('hide').removeClass('show');

	$('#alert-succes').html('');
	$('#alert-msg-info').html('');
	$('#alert-msg-warning').html('');
	$('#alert-message').html('');
}