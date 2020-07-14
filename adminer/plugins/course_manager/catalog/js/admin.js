var loader;

$(function(){
	$('#attr_group_add').click(function(){
		$('#dialog_group').dialog('open');
		$('#dialog_geb_del').button('disable');
		return false;
	});
	
	$('#brander').on('change',function(){
		$('#brandimg').attr('src',$('#brander option:selected').data('img'));
	});
	$('#brander').trigger('change');
	
	$('#attributes').on('click','.attr_group>a',function(){
		$('#dialog_group').dialog('open').dialog( "option", "title", "Изменить группу");
		$('#dialog_ge_id').val($(this).parent().data('id'));		
		$('#dialog_ge_name').val($(this).prev().text());
		return false;
	});
		
	$('#attr_add').click(function(){
		$('#dialog_attr_edit').dialog('open').dialog( "option", "title", "Добавить атрибут");
		$('#dialog_aeb_del').button('disable');
		$('#dialog_attr_edit .data_select').hide();
		return false;
	});
	
	$('#attr_sorter_dialog').dialog({autoOpen:false,height:500,width:400,title:'Фильтр по атрибутам',modal:true,resizable:false,
		buttons:[
			{text:'OK',click:function(){
				var i=1000,_order;
				var list=$('#as_sortlist');
				list.find('label').each(function(){
					var atr=$('#'+$(this).data('id'));
						_order=-1;
						if($(this).find('input:checked').size()){
							_order=i;
							i--;
						}
						atr.find('input').val(_order);
				});
				$('#cat_templ').val($('#as_cats').val());
				$(this).dialog("close");
			}},{text:'Отмена',click:function(){
				$(this).dialog("close");
			}}
		],open:function(){
			var list=$('#as_sortlist').html('');
			var orders=[],val;
			$('.attr_item').each(function(){
				var _ccb=$(this).find('input:checked');
				if(_ccb.size()) 
					orders.push([$(this).attr('id'),_ccb.val()*1]);
			});
			orders.sort(usort);
			console.log(orders);
			for (var key in orders) {
				atr = $('#'+orders[key][0]);
				val = orders[key][1];
				list.append('<label data-id="'+orders[key][0]+'"><input type="checkbox" '+(val>-1?'checked="checked"':'')+'/>'+$('#'+orders[key][0]+' label').text()+'</label>');
			}
			$("#as_sortlist").sortable({axis:"y"});
			$(this).dialog('open');
		}
	});
	
	$('#attr_sorter').click(function(){
		$('#attr_sorter_dialog').dialog('open');
		return false;
	});
	
	function usort(a, b) {
		console.log(a+' vs '+b);
		if (a[1] < b[1]) return 1;
		if (a[1] > b[1]) return -1;
	}
	
	$('#dialog_group').dialog({autoOpen:false,title:'Добавить группу',modal:true,resizable:false,
		buttons:[
	{text:'Удалить',id:'dialog_geb_del',click:function(){
		if(!confirm("Удалить группу?")) return false;
		var data={id:$('#dialog_ge_id').val()};
		$.post('hand/h_admin.php?a=group_del',data,function(response){
			if(response.status==false){
				alert('Ошибка удаления');
			} else {
				$('#group'+data.id).next().find('span').appendTo('#attr_nogroup');
				$('#group'+data.id).next().remove();				
				$('#group'+data.id).remove();				
			}	
		},"json");
		$(this).dialog("close");
	}},{text:'Отмена',click:function(){$(this).dialog("close")
	}},{text:'Сохранить',click:function(){
			var data={};
			data.name=$('#dialog_ge_name').val();
			if(data.name=='') return false;		
			$.post('hand/h_admin.php?a=group_add',data,function(response){
				if(response.status==false){
					// alert('Ошибка добавления');
				} else {
					$('#attributes').append('<h4 class="cb attr_group" data-id="'+response.status+'" id="group'+response.status+'"><span>'+data.name+'</span><a href="#">...</a></h4><div></div>');
				}	
			},"json");
			$(this).dialog("close");
		}}],open:function(){
			$('#dialog_geb_del').button('enable');
			$(this)[0].reset();
			$('#dialog_ge_id').val('0');
	}});
	
	$('#dialog_attr_edit').dialog({autoOpen:false,title:'Изменить атрибут',modal:true,resizable:false,buttons:[
	{text:'Удалить',id:'dialog_aeb_del',click:function(){
		if(!confirm("Удалить атрибут?")) return false;
		var data={id:$('#dialog_ae_id').val()};
		$.post('hand/h_admin.php?a=attr_del',data,function(response){
			if(response.status==false){
				alert('Ошибка удаления');
			} else {
				$('#attr'+data.id).remove();
			}	
		},"json");
		$(this).dialog("close");
	}},{text:'Отмена',click:function(){$(this).dialog("close")
	}},{text:'Сохранить',click:function(){
			var data={};
			data.id=$('#dialog_ae_id').val();
			data.title=$('#dialog_ae_title').val();
			data.name=$('#dialog_ae_name').val();
			data.unit=$('#dialog_ae_unit').val();
			data.group=$('#dialog_ae_group').val();
			data.type=$('#dialog_ae_type').val();
			data.value=$('#dialog_ae_value').val();
			data.options=$('#dialog_ae_opt1:checked').val()*1;
			var checker=$('#dialog_ae_id').data('checked');
			if(data.name=='')  return false;
			// progress_show();
			$.post('hand/h_admin.php?a=attr_add',data,function(response){
				if(response.status==false){
					// alert('Ошибка добавления');
				} else {
					var unit=(data.unit!='')?' ('+data.unit+')':'';
					if(data.id>0) $('#attr'+data.id).remove(); else
						data.id=response.status;
					$('#group'+data.group).next('div').append('<span class="attr_item" id="attr'+data.id+'" data-info="'+JSON.stringify(data).replace(/\"/g, "\'")+'"><label><input type="checkbox" '+(checker?'checked="checked"':'')+' name="attr[]" value="'+data.id+'" />'+data.title+unit+'</label> <a href="#">&hellip;</a></span>');
					
				}	
				// progress_hide();
			},"json");
			//},"json").success(function() {})*/;
			$( this ).dialog( "close" );		
		}}],open:function(){
			attr_groups_update();
			$('#dialog_aeb_del').button('enable');
			$(this)[0].reset();
			$('#dialog_ae_id').val('0');
	}});
	
	
	$('#dialog_ae_title, #tvr_name').on('blur',function(){
		var alias=$('#'+$(this).data('alias'));
		if(alias.val()==''){
			var data={};
			data.text=$(this).val();
			data.table='attrs';
			data.field='name';
			if($(this).attr('id')=='tvr_name'){
				data.table='tvrs';
				data.field='alias';
			}
			// alert(data.text);
			$.post('hand/h_admin.php?a=get_alias',data,function(response){
				if(response.status==false){
					alias.val(transliteIt(data.text));
				} else {
					alias.val(response.value);
				}
			},"json");
		}

	});
	
	$('#attributes').on('click','.attr_group',function(){
		var obj=$(this).next();
		if(obj.css('display')=='none')
		obj.slideDown(300); else
		obj.slideUp(300);
		return false;
	});
	
	$('#attributes').on('click','.attr_item a',function(){
		$('#dialog_attr_edit').dialog('open');
		var info=JSON.parse($(this).parent().data('info').replace(/\'/g, "\""));
		console.log(info);
		$('#dialog_ae_id').val(info.id).data('checked',$(this).parent().find('label input').prop('checked'));		
		$('#dialog_ae_title').val(info.title);
		$('#dialog_ae_name').val(info.name);
		$('#dialog_ae_unit').val(info.unit);
		$('#dialog_ae_group').val(info.group);
		$('#dialog_ae_type').val(info.type);
		if(info.type==7) $('#dialog_attr_edit .data_select').show(); else $('#dialog_attr_edit .data_select').hide();
		$('#dialog_ae_value').val(info.value);
		$('#dialog_ae_opt1').prop('checked',(info.options & 1)?'checked':'');
		return false;
	});
	
	$('#dialog_ae_type').on('change',function(){
		// alert($(this).val());
		if ($(this).val()==7)		
			$('#dialog_attr_edit .data_select').slideDown(300);
		else $('#dialog_attr_edit .data_select').slideUp(300);
	});
	
	$('#attr_templ_del').on('click',function(){
		var tovars=$('#attr_tovars_total').text()*1;
		var mess=(tovars>0)?tovars+' товаров используют этот шаблон! Удалить шаблон?\r\n':'Удалить шаблон?';
		if(!confirm(mess)) return false;
	});
	
	$('#tvrs_category').change(function(){
		//переместить товар
		if($('.sn_ckecker:checked').length>0){
			if(!confirm('Переместить выбранные объекты в другую категорию?')) return false;
			var data={};
			data.cat=$('#tvrs_category').val();
			data.tovar=[];
			$('.sn_ckecker:checked').each(function(){
				data.tovar.push($(this).val());
			});
			$.post('hand/h_admin.php?a=tvr_replace',data,function(response){
				if(response.status==false){
					alert('Ошибка перемещения');
				} else {
					document.location.href = document.location.href;
				}
			},"json");
			return true;
		}
		//смена категории
		var p=location.href.split('&');
		var nh='?p=tovars', f1=false;
		for(var i=1;i<p.length;i++){
			var t=p[i].split('=');
			switch(t[0]){
				case 'cat': nh+='&'+t[0]+'='+$('#tvrs_category').val(); f1=true; break;
				default: nh+='&'+p[i];
			};
		};
		if(!f1) nh+='&cat='+$('#tvrs_category').val();
		location.href=nh;
	});	
	
	$('#filter_having').on('change',function(){
		$('#tvrs_filter').submit();
	});	
	
	$('#sel_hide').on('change',function(){
		if($(this).val()==1) $(this).addClass('attention'); else $(this).removeClass('attention'); 
	});		
	bindUploader();
	check_types_valid();
	tovar_retempl();
	tovar_delete();
	tovar_edit();
	brand_save();
	
	$(".type_4").datepicker({
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		selectOtherMonths: true,
		dateFormat: "yy-mm-dd"
	}).inputmask('9999-99-99');	
	$('.spinner').spinner({min:-1});
		
	$(".type_6").each(function(){
		var textarea=$(this);
		CKEDITOR.inline($(this).attr('id'),{removeButtons: 'Save,NewPage,Print,Templates,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Button,Select,ImageButton,HiddenField,Image,Flash,HorizontalRule,Smiley,PageBreak,Iframe,Anchor,CreateDiv,Blockquote,Outdent,Indent,RemoveFormat,Preview,Cut,Copy,Find,Replace,Language,BidiRtl,BidiLtr,About,Maximize',height:300,width:500}).on('blur', function(){textarea.html(this.getData());});
		CKEDITOR.focusManager._.blurDelay=0;
	});
	
	$('.img_list').on('click','.delete',function(){
		$(this).parent().data('del',1).append('<a class="revive" href="#">Восстановить</a>');
		return false;
	});
	$('.img_list').on('click','.revive',function(){
		$(this).remove().parent().data('del','');
		// $(this).parent().data('del','');
		// $(this).remove();
		return false;
	});
	
});

function brand_save(){
	$('#brand_save').on('click',function(){
		loader_show('Создание изображений...');
		var url=$(this).attr('href');
		var data={};
		data.id=$('#brand_editor').data('tid')*1;
		data.name=$('#brand_name').val();
		data.url=$('#brand_url').val();
		data.descr=$('#attr_brdescr').val();
		var img=$('.img_list>div');		
		data.imgs={file:img.data('id'),new:img.data('new')*1, del:img.data('del')*1};
		$.post('hand/h_admin.php?a=brand_add',data,function(response){
			if(response.status!=false){
				document.location.href=url;
				loader_hide();
				return true;
			}else{
				loader_hide();
				alert('Ошибка сохранения');
				return false;
			}
		},"json");
		return false;
	})
}

function tovar_edit(){
	$('#tvrs_save').on('click',function(){
		loader_show('Создание изображений...');
		var url=$(this).attr('href');
		var data={};		
		data.id=$('#tovar_editor').data('tid')*1;
		data.basic=$('#tovar_basic_props .def_val').serializeArray();
		data.basic[data.basic.length]={name:'opts',value:$('#sel_hide').val()};	//переписать, когда появятся еще опции
		data.attrs=$('#attributes .attr_val').serializeArray();
		data.imgs={};
		var i=0;
		$('.img_list>div').each(function(){	
			data.imgs[i]={caption:$(this).find('input').val(),
						  file:$(this).data('id'),
						  new:$(this).data('new')*1,
						  del:$(this).data('del')*1};
			i++;
		});
		$.post('hand/h_admin.php?a=tvr_add',data,function(response){
			if(response.status!=false){
				document.location.href=url;
				loader_hide();
				return true;
			}else{
				loader_hide();
				alert('Ошибка сохранения');
				return false;
			}
		},"json");
		return false;
	});
}

function tovar_delete(){
	$('#tvrs_del').on('click',function(){		
		if(!confirm("Удалить? Действие необратимо!")) return false;
	});
}

function tovar_retempl(){
	$('#tvrs_category2').on('change',function(){
		$('#tovar_retempl').attr('href',$('#tovar_retempl').data('href')+$(this).val());
	});
	return true;
}

function check_types_valid(){
	$('.type_2').on('blur',function(){
		if(!(Number.isInteger($(this).val()*1)))		
			$(this).val('');
	});
	$('.type_3').on('blur',function(){
		var val=$(this).val();
		val=val.replace(',','.');
		if(isNaN(val*1))	
			$(this).val('');
	});
}

function attr_groups_update(){
	$('#dialog_ae_group').html('');
	$('h4.attr_group').each(function(){
		$('#dialog_ae_group').append('<option value="'+$(this).data('id')+'">'+$(this).find('span').text()+'</option>');
	});
}


function bindUploader(){
	//////proigressbar/////////
	var progressbody=$('<div class="locker"><div class="progress_body"><div id="progressbar"><div class="progress-label">Ожидание...</div></div></div></div>').appendTo('body');
	
	var progressbar=$('#progressbar').progressbar({
		value: false,
		change: function() {
			progressLabel.text( progressbar.progressbar( "value" ) + "%" );
		},
		complete: function() {
			
		}
	});	
	var progressLabel = progressbar.find(".progress-label");
	//////sortable//////////
	$( ".img_list" ).sortable({placeholder: "ui-state-highlight"}).disableSelection();

	var frm=$("#uploadForm");
	if(frm.size()){
		$('a.upload_button',frm).on('click',function(){
			$('#uploader',frm).trigger('click');
			return false;
		});
		var __up=new FileUploader({
			"message_error"	:"Ошибка при загрузке", 
			"form"				:"uploadForm",
			"inputName"			:"uploader",
			"inputId"			:"uploader",
			"handler"			:"hand/h_admin.php?a=upload",
			"portion"			:1024*512,
			"onProgress"		:function(loader,percents){
				progressbar.progressbar( "value", Math.round(percents));
			},
			"onProgressGlobal"	:function(loader,pr,total){				
			},
			"onError"			:function(msg){/* MGB.err(msg); */alert('Ошибка: '+msg);},
			"supportTypes"		:["image/jpeg","image/png","image/gif"],
			"multiUpload"		:true,
			"onComplete"		:function(loader){
				var hash=loader.hash;
				$.post('hand/h_admin.php?a=check_file',{'hash':hash},function(r){
					// console.log(r);
					if(r.status==true){
						// console.log(r.ext);
						if($('#brand_image').length>0){
							$('#brand_image').html('<div data-id="'+hash+'|'+r.ext+'" data-new="1" data-del="0"><img src="temp/'+hash+'" /><a alt="Удалить" href="#" class="delete"></a></div>');
						}else							
							frm.find('.img_list').append('<div data-id="'+hash+'|'+r.ext+'" data-new="1" data-del="0"><img src="temp/'+hash+'" /><a alt="Удалить" href="#" class="delete"></a><input type="text" placeholder="описание изображения" value=""/></div>');
					}else{
						alert(r.error);
					}
				},'json');
			},
			"onCompleteAll"		:function(loader){
			},
			"onLoadStart"		:function(loader){
				progressbody.show();
				frm.attr('data-file',0);
			},
			"onLoadEnd"			:function(loader){
				progressbody.hide();
				document.getElementById(loader.options.inputId).value="";				
			}
		});
		frm.on('submit',function(){			
			return false;
		});
	}

}

function loader_show(t){	
	loader=$('<div class="locker"><div class="progress_body loader">'+t+'</div></div>').appendTo('body').show();
	// loader=setTimeout(function(){
	// },100);
}

function loader_hide(){
	// clearTimeout(loading);
	// $('#loading').hide();
	loader.remove();
}

function show_success(text){
	$('#dialog_ok').html(text).fadeIn(300).delay(800).fadeOut(300);
}