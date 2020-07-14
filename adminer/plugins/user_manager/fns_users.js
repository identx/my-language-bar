var loading;

$(function() {
	$("#tabs").tabs();	
	$(".button").button();
	bind_submits();
});

function progress_show(){
	loading=setTimeout(function(){$('#loading').show();},100);
}

function progress_hide(){
	clearTimeout(loading);
	$('#loading').hide();
}

function show_success(text,ok){
	if(ok==false){
	$('#dialog_false').html(text).fadeIn(300).delay(1000).fadeOut(300);	
	}
	$('#dialog_ok').html(text).fadeIn(300).delay(1000).fadeOut(300);
}


function bind_submits(){
	$('#firstsubmit').on('click',function(){
		var name=$('#username').val();
		var mail=$('#usermail').val();
		var curpswd=$('#curpswd').val();
		var newpswd=$('#newpswd').val();
		var newpswd2=$('#newpswd2').val();
		progress_show();
		$.post(plugin_dir+'/handlers/hand.php',{"mode_h":"changereq","name":name,"mail":mail,"curpswd":curpswd,"newpswd":newpswd,"newpswd2":newpswd2},function(r){
		if (r.status){
			show_success(r.msg);
		} else {show_success(r.error,false);}
		},"json");
		progress_hide();
		$('#curpswd').val('');
		$('#newpswd').val('');
		$('#newpswd2').val('');
	return false;
	});
	
	$('#adduser').on('click',function(){
		$('.acccheck:checked').prop('checked',false);
		$('#ulplugred').hide();
		if (($('#adduserform').height())==0){
			$('#adduserform').animate({
			height:'140px'
			});
			$('#user_ruler').animate({
			height:'0px'
			});
			return false;
		}
		var newlogin=$('#newlogin').val();
		var newpass=$('#newpass').val();
		var newpass2=$('#newpass2').val();
		var newname=$('#newname').val();
		var newemail=$('#newemail').val();
		progress_show();
		$.post(plugin_dir+'/handlers/hand.php',{"mode_h":"adduser","newlogin":newlogin,"newpass":newpass,"newpass2":newpass2,"newname":newname,"newemail":newemail},function(r){
		if (r.status){
			show_success(r.msg);
			// $('#newlogin').val('');
			// $('#newpass').val('');
			// $('#newpass2').val('');
			// $('#newname').val('');
			// $('#newemail').val('');
			// $('#adduserform').animate({
			// height:'0px'
			// });
			setTimeout(function(){location.reload();},1000);
		} else {show_success(r.error,false);}
		},"json");
		progress_hide();
		
	return false;
	});

	$('.clickableli').on('click',function(){
		$('#ulplugred').show();
		$('#is_off').prop('checked',false)
		$('#hideuserid').data('id',$(this).data('id'));
		$('#hideuserid').html('Id пользователя: '+$(this).data('id')+'<br />Login: '+$(this).data('name'));
		if (($('#user_ruler').height())==0){
			$('#user_ruler').animate({
			height:'300px'
			});
			$('#adduserform').animate({
			height:'0px'
			});
		}
		progress_show();
		$.post(plugin_dir+'/handlers/hand.php',{"mode_h":"show_user_par","id":$(this).data('id')},function(r){
			// alert(r);
			// return;
		if (r.status){
			// show_success(r.msg);
			$('#redname').val(r.u_name);
			$('#redemail').val(r.u_mail);
			$('.acccheck:checked').prop('checked',false);
			for (var i in r.idsp ){
				$('.acccheck[data-id='+r.idsp[i]+']').prop('checked',true);
				if(r.first){$('#is_off').prop('checked',true)}
				
			}
		} else {show_success(r.error,false);}
		},"json");
		// });
		progress_hide();
	});

	$('#red_change').on('click',function(){
		var rednewpass=$('#t_passnew').val();
		var rednewpass2=$('#t_passnew2').val();
		var redname=$('#redname').val();
		var redemail=$('#redemail').val();
		var userid=$('#hideuserid').data('id');
		var idsp=[];
		var first=$('#is_off').prop('checked')?0:1;
		$('.acccheck:checked').each(function(){
			 idsp.push($(this).data('id'));
		});
		progress_show();
		$.post(plugin_dir+'/handlers/hand.php',{"mode_h":"change_user_req","rednewpass":rednewpass,"rednewpass2":rednewpass2,"redname":redname,"redemail":redemail,"userid":userid,"idsp":idsp,"first":first},function(r){
			// alert(r);
			// return;
		if (r.status){
			show_success(r.msg);
			$('#t_passnew').val('');
			$('#t_passnew2').val('');
		} else {show_success(r.error,false);}
		},"json");
		// });
		progress_hide();
		return false;
	});

	$('#red_delete').on('click',function(){
		$( "#dialog_msg" ).dialog({
	      autoopen:false,
	      modal: true,
	      buttons: {
	        "Да": function() {
	        	progress_show();
		        var userid=$('#hideuserid').data('id');
				$.post(plugin_dir+'/handlers/hand.php',{"mode_h":"del_user_req","userid":userid},function(r){
				if (r.status){
					show_success(r.msg);
					setTimeout(function(){location.reload();},1000);
					return false;
				} else {show_success(r.error,false);}
				},"json");
				progress_hide();
	        },
	        "Нет": function() {
	          $( this ).dialog( "close" );
       		}
     	}
    	});
    return false;
	});

}