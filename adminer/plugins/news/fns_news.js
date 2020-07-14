var date=new Date();
var loading;
var month=date.getMonth()+1;   
var day=date.getDate()
newdate=date.getFullYear()+'-'+(month<10?'0':'')+month+'-'+(day<10?'0':'')+day;

$(function() {
	$("#tabs").tabs();
	toolbar_actions();
	news_submit();
	$( "#radio" ).buttonset();
	var d=new Date().toLocaleString();
	$("#datepicker").datepicker({changeMonth:true,
		changeYear:true, dateFormat: "yy-mm-dd",
		onSelect: function(selectedDate){
			newdate=selectedDate;
			news_list(selectedDate);
		}
	});
	$("#news_date").datepicker({changeMonth:true,
		changeYear:true, dateFormat: "yy-mm-dd",currentText: "Now"}).val(newdate);
	news_list(newdate);
	cats_list();
	last_list();
	image_getPath();
	editor=CKEDITOR.replace('editor',{removePlugins:'save,newpage,forms,scayt,SpellChecker',height:350});
		
});

function openKCFinder_singleFile(callback_function) {
	window.KCFinder = {};
	window.KCFinder.callBack = function(url) {
		callback_function(url);
		window.KCFinder = null;
	};
	window.open('kcfinder/browse.php?type=images&langCode=ru&dir=images/news', 'kcfinder_single','width=940, height=600, top='+((screen.height-600)/2)+',left='+((screen.width-900)/2)+', resizable=yes, scrollbars=no, status=no,titlebar=no,toolbar=no');
}

function setImagePath(path) {
	$('#news_img').val(path);
	$('#news_img_show').attr('src',path);
}

function image_getPath(){
	$('#news_img_select').click(function(){
		openKCFinder_singleFile(setImagePath);
	});
}

function toolbar_actions(){
	$('#tb_add').click(function(){
		$('#editor_container select').val('');
		$('#editor_container textarea').val('');
		$('#editor_container input[type=text],#editor_container input[type=hidden]').val('');
		editor.setData('');
		$('#news_link').html('');
		$('#news_img_show').attr('src','');
		////////////		
		var date=new Date();
		var month=date.getMonth()+1;   
		var day=date.getDate()
		newdate=date.getFullYear()+'-'+(month<10?'0':'')+month+'-'+(day<10?'0':'')+day;
		$("#news_date").val(newdate);
		////////////
		$('#newslist a').removeClass('news_selected');
		$('#editor_save').val('Добавить');
		return false;
	});
	$('#tb_del').click(function(){
		news_delete($('#newslist .news_selected').data('id'));
		return false;
	});
	$('#tb_del_line').click(function(){
		news_delete($('#lastlist .news_selected').data('id'));
		return false;
	});
	$('#tb_cat_del').click(function(){
		cat_delete($('#catslist .news_selected').data('id'));
		return false;
	});
	$('#tb_cat_add').click(function(){
		cat_add();
		return false;
	});
};

function news_list(date){
	var res='';
	progress_show();
	$.post(plugin_dir+'/handlers/news_list.php',{date:date},function(response){
		if(response.status=='false'){
			$('#newslist').html('<br /><br /><br />Новости отсутствуют');
			// alert(response.sql);
		} else {
			for(var key in response.result){
				var one=response.result[key];
				var noshow=(one.show=='0')?'class="noshow"':'';
				res+='<li><a href="#" '+noshow+' data-id="'+one.id+'">'+one.name+'</a></li>';
			}
			$('#newslist').html(res);
			$('#newslist a').click(function(){
				news_select($(this).attr('data-id'));
				$('#newslist a').removeClass('news_selected');
				$(this).addClass('news_selected');
				return false;
			});
		}		
	},"json").success(function() {progress_hide();});
}

function news_select(id){
	progress_show();
	$.post(plugin_dir+'/handlers/news_select.php',{id:id},function(response){
		if(response.status=='false'){
			alert('Ошибка выборки элемента');
		} else {
			var one=response.result[0];
			var date=one.date.split(' ')[0];
			$('#news_id').val(one.id);
			// $('#news_title').val(stripslashes(one.name));
			$('#news_title').val((one.name));
			$('#news_keyw').val((one.keyws));
			$('#news_source').val((one.source));
			$('#news_announce').val((one.anounce));
			$('#news_img').val((one.img));
			$('#news_img_show').attr('src',one.img);
			$('#news_date').val(date);
			$('#news_imp').find('option[value='+one.imp+']').prop('selected','selected');
			$('#news_show').find('option[value='+one.show+']').prop('selected','selected');
			$('#news_category').find('option[value='+one.category+']').prop('selected','selected');
			editor.setData(one.text);	
			/////////////////
		
			/////////////////			
			href='/'+news_page+'/'+date.split('-')[0]+'/'+date.split('-')[1]+'/'+date.split('-')[2]+'/'+one.id+'-'+one.alias+'.html';
			$('#news_link').html('<a href="'+href+'" target="_blank">'+href+'</a>');
			$('#editor_save').val('Сохранить');
		}		
	},"json").success(function() {progress_hide();});
}

function news_submit(text){
	$('#editor_container').submit(function(){
		if ($('#news_title').val()=='' || $('#news_title').val()=='' || $('#news_announce').val()=='' || $('#news_date').val()=='' || editor.getData()==''){
			alert('Поля, помеченные звездочкой (*) и текст новости обязательны для заполнения');
			return false;
		}
		var data={};
		data.name=$('#news_title').val();
		data.keyw="0";
		data.anno=$('#news_announce').val();
		data.img=$('#news_img').val();
		data.date="0000-00-00 00:00:00";
		data.imp="0";
		data.show=$('#news_show').val();
		data.cat="0";
		data.text=editor.getData();
		data.source="0";
		data.id=$('#news_id').val();
		
		if(parseInt($('#news_id').val())>0){
			// news_update($('#news_id').val(),$('#news_title').val(),$('#news_keyw').val(),$('#news_announce').val(),$('#news_img').val(),$('#news_date').val(),$('#news_imp').val(),$('#news_show').val(),$('#news_category').val(),editor.getData(),$('#news_source').val());
			news_update(data);
		} else {
			// news_insert($('#news_title').val(),$('#news_keyw').val(),$('#news_announce').val(),$('#news_img').val(),$('#news_date').val(),$('#news_imp').val(),$('#news_show').val(),$('#news_category').val(),editor.getData(),$('#news_source').val());			
			news_insert(data);			
		}
		return false;
	});
}

function news_update(data){
	progress_show();
	$.post(plugin_dir+'/handlers/news_update.php',data,function(response){
		if(response.status=='false'){
			prompt('Ошибка',response.sql);
		} else {
			$('#newslist a[data-id='+data.id+']').html(data.name);
			show_success('&nbsp; &nbsp; Сохранено');
		}	
	},"json").success(function() {progress_hide();});
}

function news_delete(id){
	if (isNaN(id)) return false;
	$('#dialog-del').dialog({	
		modal:true,
		title:'Удаление',		
		buttons: {
			"Удалить": function() {
				$.post(plugin_dir+'/handlers/news_delete.php',{id:id},function(response){
					if(response.status=='false'){
						alert('Ошибка удаления');
					} else {
						news_list(newdate);
						last_list();
						$('#dialog-del').dialog("destroy");
						$('#tb_add').trigger("click");
						show_success('Новость удалена');
					}
				},"json");
			},
			"Отмена": function() {
				$(this).dialog( "destroy" );
			}
		}
	});	
}


// function news_insert(name,keyw,anno,img,date,imp,show,cat,text,source){
function news_insert(data){
	//попробовать serialize
	$.post(plugin_dir+'/handlers/news_insert.php',data,function(response){
		if(response.status=='false'){
			alert('Не удалось добавить новость');
		} else {
			$('#tb_add').trigger("click");
			news_list(newdate);
			show_success('Новость добавлена');
		}		
	},"json");
}

function cats_list(){
	var res='',select='<option value="0">Стандартная</option>';
	$.post(plugin_dir+'/handlers/cats_list.php',{},function(response){
		if(response.status=='false'){
			$('#catslist').html('<br /><br /><br />Категории отсутствуют');
		} else {
			for(var key in response.result){
				var one=response.result[key];
				res+='<li><a href="#" data-id="'+one.id+'">'+one.name+'</a></li>';
				select+='<option value="'+one.id+'">'+one.name+'</option>';
			}
			$('#news_category').html(select);
			$('#catslist').html(res);
			$('#catslist a').click(function(){
				if ($(this).hasClass('news_selected'))
					cat_edit($(this));
				$('#catslist a').removeClass('news_selected');
				$(this).addClass('news_selected');
				return false;
			});
		}		
	},"json");
}

function last_list(){
	var res='';
	$.post(plugin_dir+'/handlers/last_list.php',{},function(response){
		if(response.status=='false'){
			$('#lastlist').html('<br /><br /><br />Записи отсутствуют');
		} else {
			for(var key in response.result){
				var one=response.result[key];
				res+='<li><a href="#" data-id="'+one.id+'">['+one.date+'] '+one.name+'</a></li>';
			}
			$('#lastlist').html(res);
			$('#lastlist a').click(function(){
				news_select($(this).attr('data-id'));
				$('#lastlist a').removeClass('news_selected');
				$(this).addClass('news_selected');
				return false;
			});
		}		
	},"json");
}

function cat_edit(obj){
	if (isNaN(obj.attr('data-id'))) return false;
	$('#cat_add').val(obj.text());
	$('#dialog-cat_add').dialog({
		modal:true,
		title:'Редактирование',		
		buttons: {
			"Сохранить": function() {
				$.post(plugin_dir+'/handlers/cats_update.php',{id:obj.attr('data-id'),name:$('#cat_add').val()},function(response){
					if(response.status=='false'){
						alert('Ошибка редактирования');
					} else {
						cats_list();
						$('#dialog-cat_add').dialog("destroy");
						show_success('Категория изменена');
					}
				},"json");
			},
			"Отмена": function() {
				$(this).dialog( "destroy" );
			}
		}
	});	
}

function cat_add(){
	$('#dialog-cat_add').dialog({
		modal:true,
		title:'Добавление категории',		
		buttons: {
			"Сохранить": function() {
				if($('#cat_add').val().length>0){
				$.post(plugin_dir+'/handlers/cats_insert.php',{name:$('#cat_add').val()},function(response){
					if(response.status=='false'){
						alert('Ошибка добавления');
					} else {
						cats_list();
						$('#dialog-cat_add').dialog("destroy");
						show_success('Категория добавлена');
						$('#cat_add').val('');
					}
				},"json");
				}
			},
			"Отмена": function() {
				$(this).dialog( "destroy" );
			}
		}
	});	
}

function cat_delete(id){
	if (isNaN(id)) return false;
	var newscnt;
	$.ajax({async:false,type:"POST",url: plugin_dir+'/handlers/news_count.php',data:{id:id},dataType:'json',success: function(response){
		newscnt=response.result;
		$('#dialog-del-cat p span').html(newscnt);
		$('#dialog-del-cat').dialog({	
			modal:true,
			title:'Удаление',		
			buttons: {
				"Удалить": function() {
					$.post(plugin_dir+'/handlers/cats_delete.php',{id:id},function(response){
						if(response.status=='false'){
							alert('Ошибка удаления');						
						} else {
							cats_list();
							$('#dialog-del-cat').dialog("destroy");
							show_success('Категория удалена'); 
							if(newscnt>0){
								$('#dialog-news_move h4 span').text(newscnt.result);
								setTimeout(function(){$('#dialog-news_move select').html($('#news_category').html())},200);
								$('#dialog-news_move').dialog({
									modal:true,
									width:600,
									title:'Удаление/перемещение новостей',
									buttons: {
										"Удалить": function() {
											$.post(plugin_dir+'/handlers/news_delete_cat.php',{id:id},function(response){
												if(response.status=='false'){
													alert('Ошибка удаления');						
												} else {
													$('#tb_add').trigger("click");
													show_success('Новости удалены'); 
													news_list();
												}
											});
											$(this).dialog( "destroy" );
										},
										"Переместить": function() {
											var newcat=$('#dialog-news_move select').val();
											$.post(plugin_dir+'/handlers/news_update_cat.php',{from:id,to:newcat},function(response){
												if(response.status=='false'){
													alert('Ошибка перемещения новостей');						
												} else {
													show_success('Новости перемещены'); 
												}
											}); 
											$(this).dialog( "destroy" );
										},
										"Ничего не предпринимать": function() {
											$(this).dialog( "destroy" );
										}
									}
								});	 
							}
					 	}
					},"json"); 
					
				},
				"Отмена": function() {
					$(this).dialog( "destroy" );
				}
			}
		});
		}
	});	
}

function progress_show(){
	loading=setTimeout(function(){$('#loading').show();},100);
}

function progress_hide(){
	clearTimeout(loading);
	$('#loading').hide();
}

function show_success(text){
	$('#dialog_ok').html(text).fadeIn(300).delay(800).fadeOut(300);
}
