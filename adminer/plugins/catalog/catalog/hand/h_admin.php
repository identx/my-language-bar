<?php
	session_start(); 
	error_reporting(0);
	header("Content-Type: text/html; charset=utf-8");
	include('../../../../include/fns_db.php');
	include('../inc/tumbmaker.php');
	include('../../config.php');
	$_cp=explode(DIRECTORY_SEPARATOR,__DIR__); $_cp=$_cp[count($_cp)-2];		
	if(!isset($_SESSION["m_user"]["id"]) || (array_search($_cp,$_SESSION["m_user"]['has'])===false)) exit;
	$response=array('status'=>false);
	$conn=db_connect();
	$d=$_POST;
	$uploaddir="../temp/";
	
	switch($_GET['a']){
		/// бренды
		case 'brand_add':
			$response['status']=true;	
			if($d['id']=='0'){
				$d['id']=db_query("INSERT INTO `"._DB_PREF_."brands` (`id`) VALUES ('')");
				$response['id']=$d['id'];
			}
			$img=$d['imgs'];
			if($img['del']==1&&$img['new']==0){
				$imgfile=current(db_query("SELECT `img` FROM `"._DB_PREF_."brands` WHERE `id`='$d[id]' LIMIT 1"));
				$imgfile=$imgfile['img'];
				$response['status']=unlink('../'.$conf['path_to_brand'].$imgfile);
				$imgsql=",`img`=''";
			}
			if($img['new']==1){				
				$fname=explode('|',$img['file']);
				$ext=$fname[1];
				$fname=$fname[0];
				if($img['del']==0){
					$new_file='../'.$conf['path_to_brand'].$d['id'].'.'.$ext;
					$newimg = new SimpleImage($uploaddir.$fname);
					$newimg -> best_fit($conf['brand']['w'],$conf['brand']['h']);
					$newimg -> save($new_file);
					$imgsql=",`img`='$d[id].$ext'";					
				}
				$response['status']=unlink($uploaddir.$fname);
			}
			db_query("UPDATE `"._DB_PREF_."brands` SET `name`='".addslashes($d['name'])."',`url`='$d[url]',`descr`='".addslashes($d['descr'])."'$imgsql WHERE `id`='$d[id]'");			
		break;	
		/// товары
		case 'tvr_replace':		
			if(isset($d['cat']) && count($d['tovar'])>0){
				$lst=$d['tovar'];
				foreach($lst as $one)
					$response['status']=db_query("UPDATE `"._DB_PREF_."tvrs` SET `cat`=$d[cat] WHERE `id`=$one;")*1;
			};
		break;
		case 'tvr_add':	
			if($d['id']=='0'){
				$d['id']=db_query("INSERT INTO `"._DB_PREF_."tvrs` (`id`) VALUES ('')");
				$response['id']=$d['id'];
			}
			//атрибуты
			$batrs=array();
			foreach($d['basic'] as $ba)
				$batrs[]="`$ba[name]`='".addslashes($ba['value'])."'";
			$sql0="UPDATE `"._DB_PREF_."tvrs` SET ".implode(',',$batrs)." WHERE `id`='$d[id]'";
			db_query($sql0);
			$sql="SELECT `"._DB_PREF_."values`.*, `"._DB_PREF_."attrs`.`name`, `"._DB_PREF_."attrs`.`type` FROM `"._DB_PREF_."values` LEFT JOIN `"._DB_PREF_."attrs` ON (`"._DB_PREF_."values`.`attr`=`"._DB_PREF_."attrs`.`id`)	WHERE `"._DB_PREF_."values`.`tvr`='$d[id]'";
			$res=db_getItems($sql);
			$old_attrs=array();
			foreach($res as $attr)
				$old_attrs[$attr['name']]=$attr;
			foreach($d['attrs'] as $a){
				$new=explode('_',$a['name'],4); //0-pref 1-id 2-type 3-name
				$old=$old_attrs[$new['3']];
				$sql='';
				if(isset($old)){					
					if($a['value']!=$old['t'.$old['type']]){
						if($a['value']!='')
							$sql="UPDATE `"._DB_PREF_."values` SET `t$new[2]`='".addslashes($a['value'])."' WHERE `tvr`='$d[id]' AND `attr`='$new[1]' LIMIT 1";
						else
							$sql="DELETE FROM `"._DB_PREF_."values` WHERE `tvr`='$d[id]' AND `attr`='$new[1]' LIMIT 1";						
				}}elseif($a['value']!=''){
					$sql="INSERT INTO `"._DB_PREF_."values` (`tvr`,`attr`,t$new[2]) VALUES ('$d[id]', '$new[1]', '".addslashes($a['value'])."')";
				}
				db_query($sql);
			}
			//имаги
			foreach($d['imgs'] as $num=>$img){
				$fname=explode('|',$img['file']);
				$ext=$fname[1];
				$fname=$fname[0];
				$iid=$fname;
				if($img['del']==1&&$img['new']==0){					
					$imgfile=current(db_query("SELECT `name` FROM `"._DB_PREF_."images` WHERE `id`='$img[file]' LIMIT 1"));
					$imgfile=$imgfile['name'];
					db_query("DELETE FROM `"._DB_PREF_."images` WHERE `id`='$img[file]' LIMIT 1");
					foreach($conf['size'] as $pref=>$sz){
						unlink('../'.$conf['path_to_image'].$pref.'-'.$imgfile.'.'.$ext);
					}
				}
				if($img['new']==1){
					if($img['del']==0){
						$s=getimagesize($uploaddir.$fname);
						$sql="SHOW TABLE STATUS LIKE '"._DB_PREF_."images'";
						$iid=current(db_query($sql));
						$iid=$iid['Auto_increment'];
						$ok=true;
						foreach($conf['size'] as $pref=>$sz){
							$new_file='../'.$conf['path_to_image'].$pref.'-'.$iid.'.'.$ext;
							$newimg = new SimpleImage($uploaddir.$fname);
							$newimg -> best_fit($sz['w'],$sz['h']);
							$newimg -> save($new_file);
							$ok&=file_exists($new_file);
						}
						if($ok){
							$iid=db_query("INSERT INTO `"._DB_PREF_."images` (`id`,`id_tvr`,`index`,`name`,`caption`) VALUES ('','$d[id]','$num','$iid.$ext','".addslashes($img['caption'])."')");						
						}						
					}
					unlink($uploaddir.$fname);
				}else{
					db_query("UPDATE `"._DB_PREF_."images` SET `index`='$num',`caption`='".addslashes($img['caption'])."' WHERE `id`='$fname'");
				}
			}
			$response['status']=true;
		break;	
		/// шаблоны и атрибуты
		case 'group_add':
			if($d['id']>0)
				$sql="UPDATE `"._DB_PREF_."attr_groups` SET `name`='".addslashes($d['name'])."'";
			else 
				$sql="INSERT INTO `"._DB_PREF_."attr_groups` VALUES('','".addslashes($d['name'])."')";
			$response['status']=db_query($sql)*1;
		break;
		case 'group_del':
			$sql="DELETE FROM `"._DB_PREF_."attr_groups` WHERE `id`='$d[id]'";
			$res=db_query($sql)*1;
			if($res>0){
				$sql="UPDATE `"._DB_PREF_."attrs` SET `group`=0 WHERE `group`='$d[id]'";
				db_query($sql);
			}
			$response['status']=$res;
		break;
		case 'attr_add':
			if($d['id']>0){
				$sql="UPDATE `"._DB_PREF_."attrs` SET `name`='$d[name]', `title`='".addslashes($d['title'])."',`type`='$d[type]',`value`='".addslashes($d['value'])."',`unit`='".addslashes($d['unit'])."',`options`='$d[options]',`group`='$d[group]' WHERE `id`='$d[id]'";
			} else
				$sql="INSERT INTO `"._DB_PREF_."attrs` VALUES('','$d[name]','".addslashes($d['title'])."','$d[type]','".addslashes($d['value'])."','".addslashes($d['unit'])."','$d[options]','$d[group]')";
			$response['status']=db_query($sql)*1;
		break;
		case 'attr_del':
			$sql="DELETE FROM `"._DB_PREF_."values` WHERE `attr`='$d[id]'";
			db_query($sql);
			$sql="DELETE FROM `"._DB_PREF_."attrs` WHERE id='$d[id]' LIMIT 1";			
			$response['status']=db_query($sql)*1;			
		break;		
		case 'get_alias':		
			require_once("../inc/fns_translit.php");
			$text=translit_alias($d['text']);
			$n='';
			// $blacklist=('id','name','cat','templ','article','brand','alias','price','count');
			if (isset($d['text']))
			do{
				$text2=$text.$n;				
				$res=db_query("SELECT * FROM `"._DB_PREF_."$d[table]` WHERE `$d[field]`='$text2';");
				$n++;
			}while(count($res)>0);
			$response['status']=true;
			$response['value']=$text2;	
		break;
		case 'check_file':
			$file=$uploaddir.$_POST['hash'];
			if(filesize($file)<=4*1024*1024){
				$mime=getimagesize($file);
				$mime=$mime["mime"];
				$types=array('image/png' => 'png','image/jpeg' => 'jpg','image/gif' => 'gif','image/bmp' => 'bmp','image/vnd.microsoft.icon' => 'ico','image/tiff' => 'tif','image/svg+xml' => 'svg');
				if($types[$mime]!=''){
					$response['status']=true;
					$response['ext']=$types[$mime];
				}else{
					$response['status']=false;
					$response['error']='Тип файла не поддерживается';
				}
			}else{
				@unlink($file);
				$response['status']=false;
				// @unlink($file.'.ready');
				$response['error']='Файл превышает допустимый размер';
			}
		break;
		case 'upload':
			$hash=$_SERVER["HTTP_UPLOAD_ID"];		
			if(!is_dir($uploaddir))
				@mkdir($uploaddir,0775,true);
			$filename=$uploaddir."/".$hash."";
			if (intval($_SERVER["HTTP_PORTION_FROM"])==0) 
				$fout=fopen($filename,"wb");
			else
				$fout=fopen($filename,"ab");
			if (!$fout){
				$response['status']=false;
				$response['error']="Can't open file for writing.";
				return;
			}else{
				$fin=fopen("php://input","rb");
				if($fin){
					while(!feof($fin)){
						$data=fread($fin,1024*1024);
						fwrite($fout,$data);
					}
					fclose($fin);
				}
				fclose($fout);
				$response['status']=true;
			}
		break;
		default:
			$response['status']=false;
		break;
	}
	
	db_disconnect($conn);
	echo json_encode($response);
	//ыыы
	

?>
