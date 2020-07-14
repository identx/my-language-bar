function alertObj(obj) { 
	var str = ""; 
	for(k in obj) { 
		str += k+": "+ obj[k]+"\r\n"; 
	} 
	alert(str); 
}

function transliteIt(string){
	var dictionary = {'а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ж':'g', 'з':'z', 'и':'i', 'й':'y', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o', 'п':'p', 'р':'r', 'с':'s', 'т':'t', 'у':'u', 'ф':'f', 'ы':'i', 'э':'e', 'А':'A', 'Б':'B', 'В':'V', 'Г':'G', 'Д':'D', 'Е':'E', 'Ж':'G', 'З':'Z', 'И':'I', 'Й':'Y', 'К':'K', 'Л':'L', 'М':'M', 'Н':'N', 'О':'O', 'П':'P', 'Р':'R', 'С':'S', 'Т':'T', 'У':'U', 'Ф':'F', 'Ы':'I', 'Э':'E', 'ё':'yo', 'х':'h', 'ц':'ts', 'ч':'ch', 'ш':'sh', 'щ':'shch', 'ъ':'', 'ь':'', 'ю':'yu', 'я':'ya', 'Ё':'YO', 'Х':'H', 'Ц':'TS', 'Ч':'CH', 'Ш':'SH', 'Щ':'SHCH', 'Ъ':'', 'Ь':'',
	'Ю':'YU', 'Я':'YA', ' ':'-','0':'0','1':'1','2':'2','3':'3','4':'4','5':'5','6':'6','7':'7','8':'8','9':'9','-':'-','_':'_'};
	return string.replace(/[\s\S]/g, function(x){
		if(dictionary.hasOwnProperty(x))
			return dictionary[x];
		if((x>='a' && x<='z') || (x>='A' && x<='Z')) return x;
		return '';
	});
};


$(window).load(function(){
	$('.btn,.hvr').hover(function(){ $(this).addClass('ui-state-focus'); },function(){ $(this).removeClass('ui-state-focus'); });
	
	$('.mas_del').click(function(){
		if(confirm('Вы действительно хотите удалить элемент структуры и все вложенные?')){
			var url=$(this).parent().find('a').attr('href')+'&del';
			alert(url);
			var t=$(this);
			$.get(url,{},function(d){
				if(d.length>=3){ alert('Не удалось удалить элемент'); return true; };
				t.parent().parent().remove();
			});
		}else{return false};
	});
	$('.tvr_del').click(function(){
		if(confirm('Вы действительно хотите удалить запись?')){
			var url=$(this).parent().find('a').attr('href')+'&del';
			var t=$(this);
			$.get(url,{},function(d){
				if(d.length>=3){ alert('Не удалось удалить запись'); return true; };
				document.location='?p=tvrs';
			});
		}else{return false};
	});
	
	$('#mas_scont').draggable({
		cursor:"move",
		distance: 10,
		axis:"x",
		stop:function(e,u){
			if(u.helper.position().left>0)	u.helper.css('left','0');
			if(u.helper.position().left+u.helper.width()<$('#mas_cont').width()) u.helper.css('left','-'+(u.helper.width()-$('#mas_cont').width())+'px');
		}
	}).each(function(){
		var w=0;
		$(this).find('.mas,.mas_ntc').each(function(){ var tw=$(this).width()+$(this).position().left+10; if(tw>w) w=tw; });
		$(this).width(w);
	});
	$('#t_t_addattr').click(function(){
		$('#t_t_addattr_dialog').dialog({
			draggable:false,resizable:false,modal:true,
			buttons: {
				"Отмена": function(){
					$(this).dialog("close");
				},
				"Добавить": function(){
					var name=$('#t_t_dname').val(),
						title=$('#t_t_dtitle').val(),
						type=$('#t_t_dtype').val(),
						typename=$('#t_t_dtype option[value="'+type+'"]').attr('rel'),
						list=$('#t_t_dlist').val();
						unit=$('#t_t_dunit').val();
						opt1=$('#t_t_dopt1').prop("checked")?'1':'0';
					if(name.length<=0){ $('#t_t_dname').focus(); return; };
					if(title.length<=0){ $('#t_t_dtitle').focus(); return; };
					$('#t_t_ilist .cb').before('<span class="fl d_inl_bl w49p"><label><input type="checkbox" checked="checked" name="t_t_anew[]" value="'+name+'|'+title+'|'+type+'|'+list+'" />'+title+' ('+typename+')</label></span>');
					$(this).dialog("close");
				}
			},
			open: function(event, ui){	
				$('#t_t_dname,#t_t_dtitle,#t_t_dtype,#t_t_dlist').val('');
				var t=Math.floor(($('body').css({'overflow':'hidden'}).height()-$(event.target).parent().height())/2);
				// $(event.target).parent().css({'top':t+'px'});
			},
			close: function(){
				$('body').css({'overflow':'auto'});
			}});
	});
	$('#tvr_cat').change(function(){
		var cnt=0;
		$('.t_tvr_chb').each(function(){
			if($(this).attr('checked')=='checked')
				cnt++;
		});
		if(cnt>0){
			$('#t_tvr_form_chb').submit();
			return;
		};
		var p=location.href.split('&');
		var nh='?p=tvrs', f1=false, f2=false;
		for(var i=1;i<p.length;i++){
			var t=p[i].split('=');
			switch(t[0]){
				case 'cat': nh+='&'+t[0]+'='+$('#tvr_cat').val(); f1=true; break;
				default: nh+='&'+p[i];
			};
		};
		if(!f1) nh+='&cat='+$('#tvr_cat').val();
		location.href=nh;
	});
	$('#sel_all_tvr').change(function(e){ $('input[type="checkbox"].t_tvr_chb').attr('checked',this.checked); });
	
	var sid=$('input#sid').val();
	var tstamp=$('input#tstamp').val();
	var imgs=$('input#imgs_name').val();
	
	
	

	$('input.im_ttl').keyup(function(e){
		$(this).parent().find('input.im_name').val(transliteIt($(this).val()));
	});
	$('#img_cont').sortable({
		cursor:"move",
		axis:"y",
		stop: function(event,ui){
			var s='';
			$('#img_cont .img_r').each(function(){ s+=(s.length>0?'|':'')+$(this).attr('rel'); });
			$('#imgs_sort').val(s);
		},
	});
	$('.img_del').click(function(){
		if(confirm('Вы действительно хотите удалить изображение?')){
			var id=$(this).parent().parent().attr('rel');
			$(this).parent().parent().css('opacity',0.2).attr('del','ok').find('input,textarea').attr('disabled','disabled').removeAttr('name');
			$(this).remove();
			var s='';
			$('.img_r[del="ok"]').each(function(){ s+=(s.length>0?'|':'')+$(this).attr('rel'); });
			$('#imgs_del').val(s);
		};
	});
	$('form.w7>input[name="name"]').on('blur',function(){
		if($('form.w7>input[name="url"]').val()=='')
		$('form.w7>input[name="url"]').val(transliteIt(text=$(this).val()));
	});
	

	
	$('.datepicker').datepicker({showOn: "button", dateFormat: "yy-mm-dd",changeMonth:true,
		changeYear:true});
	if($('.superselect').size())
	$('.superselect').select2();

});