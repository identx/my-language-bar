<?php
	// require_once('fsnDb.php');
/*
	v.1.5.4 UPD: fcomment, PHP_EOL
	v.1.5.5 UPD: исправлен случайный порядок поиска при равной релевантности
	v.1.6.0 UPD: добавлен setItem
	v.1.6.1 UPD: исправлена ошибка потери значений атрибутов в getCatFilters
	v.1.6.2 UPD: setItem теперь копирует и attrs_hid
	v.1.7 UPD:  добавлена ф-я log()
	v.1.8.1 UPD:  добавлена загрузка изображений
	v.1.8.2 UPD:  setItemsLimit fix
	v.1.9 UPD:  критический фикс для setItem, showHiddens
	v.1.9.1:  исправлена выдача поиска
	v.1.9.2: исправлена ошибка при получении товара с несколькими картинками
    v.1.9.3: добавление name_def и alias при создании товара
	v.1.9.4: исправлена ошибка с запросом SHOW TABLE STATUS
	
	
	Catalog($pref='mcat_')	 - конструктор, параметром передается префикс таблиц
	getBrands()	 - получить список брендов
	setItemsSort($fields)	 - установить сортировку по основным атрибутам
	setItemsLimit($start=0,$limit=999999)	 - установить лимит и начало выборки
	search($str)	 - поиск по полю name, возвращает список id
	setFilter($filter)	 - установить фильтр
	getItemsCount()	 - получить кол-во товаров без учета лимита
	getItems($filter=array())	 - получить список товаров с основными атрибутами и картинками
	getItem($id)	 - получить товар с дополнительными атрибутами и картинками
	getCatFilters($id)	- получить список атрибутов для фильтрации
	getCatPath($id,$asString=false)	 - получить путь до категории по id массивом/строкой
	getCatPathA($alias,$asString=false)	 - получить путь до категории по алиасу массивом/строкой
	getCatAlias($alias)	 - получить категорию по алиасу
	getCatId($id)	 - получить категорию по id
	getCats($root=0)	 - получить категории и все вложенные
	reset()	 - сброс всех фильтров, лимитов, сортировок
	showHiddens($show0,$showdisabled)	 - вкл/выкл товаров с кол-вом = 0 и скрытых товаров
	array_filter($array,$filters)	 - автономная функция фильтрации массива
	array_values_key($arr,$key)	 - автономная функция получения значений из массива по ключу
	translit($str)	 - автономная функция получения транслита
	setItem($data) - добавляет или обновляет товар (пока без имаг)
	log() - получить историю запросов
	imgAdd($array или $str);
	
	imgSize;
	imgPath;
	
	!!! для работы нужен класс работы с БД simpleMysqli
	
	***********************
	Пример использования. Получим последние 3 товара в категории и само название категории:
	$catid=7;
	$catalog=new Catalog('mcat_');
	$catalog->setItemsLimit(3);
	$catalog->setItemsSort(array('id'=>0));
	$items=$catalog->getItems(array(array('cat'=>array($catid))));
	$cat=$catalog->getCatId($catid);
	***********************
	
*/

	class Catalog{
	public	$pr; //префикс таблиц 
	public	$relevant=40; // релевантность поиска, выражено в % 
	private	$nodes;
	private	$count=-1;
	private	$show_count0=false;
	private	$show_disabled=false;
	private	$items_limit_string;
	private	$items_limit;
	private	$items_start;
	private	$items_sort;
	private	$log=[];
	
	public	$items_filter_params;
	public	$items_filter_attrs=array();
	
	public  $imgDir='';
	public	$imgSize=Array("s"=>Array("w"=>300,"h"=>300),
		"b"=>Array("w"=>1500,"h"=>1500));
	
	private function setLog($log){
		$this->log[]=$log;
		return true;
	}
	
	public function log(){
		return $this->log;
	}
	
	public function reset(){
		$this->showHiddens(false,false);
		$this->items_limit_string='';
		$this->items_limit='';
		$this->items_start='';
		$this->items_sort='';
		$this->items_filter_params='';
		$this->items_filter_attrs=array();
		$this->relevant=40;
		$this->count=-1;
		return $this;
	}
	
	public function imgPath($path){
		if (!file_exists($path))
			if(!mkdir($path, 0777)) return false;	
		$this->imgDir=$path;
		return file_exists($path);
	}
	
	public function imgSize($size){
		$this->imgSize=$size;
	}
	
	public function imgAdd($id_tovar,$imgs){
		$iid=false;
		if(!($id_tovar>0)) return false;
		$types=array(1=>'gif','jpg','png',6=>'bmp',7=>'tif',8=>'tif');
		$ok=true;
		@chdir($this->imgDir);
		if(is_string($imgs)) $imgs=[$imgs];
		$sql="SELECT `index` FROM `".$this->pr."images` WHERE `id`='$id_tovar' ORDER BY `index` DESC LIMIT 1";
		$index=db_query($sql);	
		$this->setLog($sql);	
		$index=(count($index)>0)?$index[0]['index']:0;
		foreach($imgs as $img){
			
			$ext=$types[exif_imagetype($img)];
			if($ext=='') continue;
			$sql="SHOW TABLE STATUS LIKE '".$this->pr."images'";
			$iid=current(db_query($sql));
			$this->setLog($sql);
			$iid=$iid['Auto_increment'];
			foreach($this->imgSize as $pref=>$sz){

				$new_file=$pref.'-'.$iid.'.'.$ext;
				$newimg = new SimpleImage3($img);				
				$newimg -> bestFit($sz['w'],$sz['h']);
				// if(crop) $newimg ->crop(azaza)  можно со временем проработать кроп
				$newimg->toFile($new_file);
				$ok&=file_exists($new_file);
			}
			if($ok){	
				$sql = 	"INSERT INTO `".$this->pr."images` (`id`,`id_tvr`,`index`,`name`,`caption`) VALUES ('','$id_tovar','$index','$iid.$ext','')";			
				$iid=db_query($sql);
				$this->setLog($sql);
			}
			$index++;
		}
		return $iid;
	}
	
	public function setItem($d){
		if($d['id']=='0' || !isset($d['id'])){
			$sql="INSERT INTO `".$this->pr."tvrs` (`id`,`cat`,`templ`,`article`,`brand`,`name`,`name_def`,`alias`,`price`,`price_d`,`count`) VALUES (null,0,0,0,0,'','','',0,0,0)";
			$d['id']=db_query($sql);
			$this->setLog($sql);
            $d['name_def'] = $d['name'];
            $d['alias'] = Catalog::translit($d['name']);
		}		
		$batrs=$final_values=array();
		$avalibleAttrs=Array('cat','templ','name','opts','article','brand','name_def','alias','price','price_d','count');
		foreach($avalibleAttrs as $one){
			if(isset($d['basic'][$one]))
				$final_values[$one]=$d['basic'][$one]; else
			if(isset($d[$one]))
				$final_values[$one]=$d[$one];
		}
		foreach($final_values as $key=>$one)
			$batrs[]="`$key`='".addslashes($one)."'";

		$sql0="UPDATE `".$this->pr."tvrs` SET ".implode(',',$batrs)." WHERE `id`='$d[id]'";
		db_query($sql0);
		$this->setLog($sql0);
		
		$sql="SELECT `".$this->pr."values`.*, `".$this->pr."attrs`.`name`, `".$this->pr."attrs`.`type` FROM `".$this->pr."values` LEFT JOIN `".$this->pr."attrs` ON (`".$this->pr."values`.`attr`=`".$this->pr."attrs`.`id`)	WHERE `".$this->pr."values`.`tvr`='$d[id]'";
		$res=$sql;
		$res=db_getItems($sql);
		$this->setLog($sql);
		
		$old_attrs=[];
		foreach($res as $attr)
			$old_attrs[$attr['name']]=$attr;

		$userAttrs=isset($d['attrs'])?$d['attrs']:[];
		if(isset($d['attrs_hid']))
			$userAttrs=array_merge($d['attrs'],$d['attrs_hid']);
		if(count($userAttrs))
			foreach($userAttrs as $key=>$new){
				if(!isset($old_attrs[$key]) || !isset($old_attrs[$key]['type']) || !isset($old_attrs[$key]['id'])){
					$sql="SELECT * FROM `".$this->pr."attrs` WHERE `name`='$key' LIMIT 1";
					$this->setLog($sql);
					$sql=db_getItem($sql);
					$new['type']=$sql['type'];
					$new['id']=$sql['id'];
				}	
				$sql='';
				if(isset($old_attrs[$key])){
					$old=$old_attrs[$key];
					if($new['val']!=$old['t'.$old['type']]){
						if($new['val']!='')
							$sql="UPDATE `".$this->pr."values` SET `t$new[type]`='".addslashes($new['val'])."' WHERE `tvr`='$d[id]' AND `attr`='$new[id]' LIMIT 1";
						else
							$sql="DELETE FROM `".$this->pr."values` WHERE `tvr`='$d[id]' AND `attr`='$new[id]' LIMIT 1";
					}
				}elseif($new['val']!=''){
					$sql="INSERT INTO `".$this->pr."values` (`tvr`,`attr`,t$new[type]) VALUES ('$d[id]', '$new[id]', '".addslashes($new['val'])."')";
				}
				db_query($sql);
				$this->setLog($sql);
			// echo $sql.'<br />';
			}
			return $this->getItem($d['id']);
		}


		public function getItemsCount(){
			return $this->count;
		}

		public function getBrands($ids=array()){
			$q="SELECT * FROM `".$this->pr."brands`";
			$q.=(!empty($ids))?' WHERE `id` IN ('.implode(',',$ids).')':'';
			$_res=db_query($q);
			$this->setLog($q);
			if(!count($_res)) return array();
			$res=array();
			foreach($_res as $one)
				$res[$one['id']]=$one;
			return $res;
		}

	//установка сортировки. Сортируется только по основным атрибутам
	//пример: ('price'=>1,'name'=>0) 0 - по убыванию, 1- по возрастанию
		public function setItemsSort($fields){
			if ($fields=='') {
				$this->items_sort='';
			}else{
				$sorts=array();
				foreach($fields as $key=>$one){
					$sorts[]=' `'.$key.'` '.(($one==0)?'DESC':'ASC');
				}
				$this->items_sort=' ORDER BY '.(implode(', ',$sorts));
			}
			return $this;
		}

	//установка 
		public function setItemsLimit($start=0,$limit=999999){

			if ($start<0 || $start==='' || ($start==0 && $limit==999999)) {
				$this->items_limit=$this->items_limit_string=$this->items_start='';
			}else{
				if($limit==999999){
					$limit=$start;
					$start=0;
				}
				$this->items_limit=$limit;
				$this->items_start=$start;
				$this->items_limit_string=' LIMIT '.$start.', '.$limit;
			}
			return $this->items_limit;
		}

		public function search($str){
			function cropWords($word) {
			$reg = "/(ый|ой|ая|ое|ые|ому|ему|а|о|у|е|ы|и|я|ого|ство|ых|ох|ия|ий|ь|он|ют|ат|ья)$/i"; //А если в середине слова?!?!
			$word = preg_replace($reg,'',$word);
			return $word;
		}
		$words=explode(" ",trim(preg_replace("/\s(\S{1,2})\s/"," ",preg_replace("/ +/i"," ","$str"))));
		$trueWords=Array();
		if(count($words)){
			foreach($words as $word)
				if (mb_strlen($word,'UTF-8')>1){
					if(mb_strlen($word,'UTF-8')>3)
						$word=cropWords($word);
					$trueWords[]=addcslashes(addslashes($word),'%_');
				}
			}else return array();
			$trueWords=array_unique($trueWords);
			if(!count($trueWords)) return array();
			$q=array();		
			$query="SELECT `".$this->pr."tvrs`.`id`,`cat`, `".$this->pr."nodes`.`name` ,(";
			foreach($trueWords as $word){
				$q[]="IF(`".$this->pr."tvrs`.`name` LIKE '%".addslashes($word)."%',".mb_strlen($word,'UTF-8').",0)";
				$q[]="IF(`".$this->pr."nodes`.`name` LIKE '%".addslashes($word)."%',".mb_strlen($word,'UTF-8').",0)";
			}
			$query.=implode(' + ',$q).") AS `relevant` FROM `".$this->pr."tvrs`
			LEFT JOIN `".$this->pr."nodes` ON (`".$this->pr."nodes`.`id`=`cat`)
			WHERE ".$this->__showHidden()." AND `price`>-1 AND `cat`>0  HAVING `relevant`>".(round(mb_strlen(implode($trueWords))*($this->relevant/100)))." ORDER BY `relevant` DESC, `id`";
			$res=db_query($query);
			$this->setLog($query);
			$ids=$this->array_values_key($res,'id');
			return $ids;
		}

	// фильтрация по атрибутам товара
	// array(array('price'=>array('>','1000')),array('cat'=>array('4')))
		public function setFilter($filter){
			if ($filter=='') {
				$this->items_filter_params='';
				$this->items_filter_attrs=array();
			}else{
				$base=array('id','name','cat','templ','article','brand','alias','price','price_d','count');
			$actions=array('=','>','<','<=','>=','!=','<>','in','!in','between','fixed in'); //здесь нет like. Это норма?
			$actions=array('=','>','<','<=','>=','!=','<>','in','!in','between','fixed in','like','like%','!like%');
			$fil_1=array();
			$fil_2=array();
			$attr_names=array();
			foreach($filter as $one){
				$value=current($one);
				if(!in_array($value[0],$actions) && count($value)>1)  array_unshift($value,'in');
				if(!in_array($value[0],$actions)) array_unshift($value,'=');
				switch($value[0]){
					case '=':
					case '>':
					case '>=':
					case '<':
					case '<=':
					case '<>': $val=$value[0]."'".addslashes($value[1])."'"; break;
					case '!=': $val="<>'".addslashes($value[1])."'"; break;
					case 'in': 
					array_shift($value);
					$val=' IN (\''.implode('\',\'',array_diff($value, array(''))).'\')';
					break;
					case 'fixed in': 
					array_shift($value);
					$val=' IN (\''.implode('\',\'',array_diff($value, array(''))).'\')';
					if($this->items_sort=='')
						$this->items_sort="ORDER BY FIND_IN_SET(`".key($one)."` ,'".implode(',',array_diff($value, array('')))."' )";
					break;
					case '!in': 
					array_shift($value);
					$val=' NOT IN (\''.implode('\',\'',array_diff($value, array(''))).'\')';
					break;
					case 'like':
					$val=" LIKE '%".addslashes($value[1])."%'";
					break;
					case 'like%':
					$val=" LIKE '".addslashes($value[1])."%'";
					break;
					case '!like%':
					$val=" NOT LIKE '".addslashes($value[1])."%'";
					break;
				}
				$fil=&$fil_1;
				if(!in_array(key($one),$base)) {
					$fil=&$fil_2;
					$attr_names[]=key($one);
				}
				$fil[]=(!in_array(key($one),$base))?array(key($one)=>$val):'`'.$this->pr.'tvrs`'.'.`'.key($one).'`'.$val;
			}
			if(count($fil_1))
				$this->items_filter_params.=' AND '.implode(' AND ',$fil_1);
			
			if(count($fil_2)){
				$q="SELECT `id`,`type`,`name` FROM `".$this->pr."attrs` WHERE `name` IN ('".implode("','",$attr_names)."')";
				$attrs=db_query($q);
				$this->setLog($q);
				$_attrs=array();
				foreach($attrs as $key=>$one){
					$_attrs[$one['name']]=array('id'=>$one['id'],'tp'=>$one['type']);
				}		
				foreach($fil_2 as $one){
					if(count($_attrs[key($one)])){
						$this->items_filter_attrs[]="`attr`=".$_attrs[key($one)]['id']." AND `t".$_attrs[key($one)]['tp']."`".current($one);
					}
				}
			}
		}
		return $this;
	}
	
	public function getItems($filter=array()){

		if(count($filter)) $this->setFilter($filter);
		if(count($this->items_filter_attrs)==0){
			$q="SELECT `".$this->pr."tvrs` .*, (SELECT `name` FROM `".$this->pr."images` WHERE `id_tvr`=`".$this->pr."tvrs`.`id` ORDER BY `index` ASC LIMIT 1) as 'img'  FROM `".$this->pr."tvrs` WHERE  ".$this->__showHidden().$this->items_filter_params." ".$this->items_sort.$this->items_limit_string;
			$res=db_query($q);
			$this->setLog($q);

			if($this->items_limit_string!=''){
				$q="SELECT COUNT(*) `c` FROM `".$this->pr."tvrs` WHERE ".$this->__showHidden().$this->items_filter_params;
				$cnt=db_query($q);
				$this->setLog($q);
				if(empty($cnt)) return array();
				$cnt=current(db_query($q)); //??????    может $cnt=current($cnt);???
				$this->setLog($q);
				$this->count=$cnt['c'];
			} else $this->count=empty($res)?0:count($res);
			
		} else {
			$q="SELECT `id` FROM `".$this->pr."tvrs` WHERE ".$this->__showHidden().$this->items_filter_params;
			$ids=db_query($q);
			$this->setLog($q);
			$ids=$this->array_values_key($ids,'id');		
			foreach($this->items_filter_attrs as $attr){
				$q="SELECT `tvr` as `id` FROM `".$this->pr."values` WHERE ".$attr;
				$_ids=$this->array_values_key(db_query($q),'id');
				$this->setLog($q);				
				$ids=array_intersect($ids,$_ids);
				if(!count($ids)) return array();
			}
			$this->count=count($ids);
			$q="SELECT `".$this->pr."tvrs` .*, (SELECT `name` FROM `".$this->pr."images` WHERE `id_tvr`=`".$this->pr."tvrs`.`id` AND `".$this->pr."images`.`index`=0 LIMIT 1) as 'img'  FROM `".$this->pr."tvrs` 	
			WHERE `".$this->pr."tvrs`.`id` IN (".implode(',',$ids).") ".$this->items_sort.$this->items_limit_string;
			$res=db_query($q);
			$this->setLog($q);
		}
		if(empty($res)) return array();
		foreach($res as &$one){
			$one['path']=$this->getCatPath($one['cat'],1);
			
		}
		return $res;
	}
	
	public function getItem($id){
		$q="SELECT * FROM `".$this->pr."tvrs` WHERE ".$this->__showHidden()." AND `id`='$id' LIMIT 1";
		$item=current(db_query($q));	
		$this->setLog($q);
		if(!count($item)) return false;
		$q="SELECT `".$this->pr."attrs`.*, `".$this->pr."attr_groups`.`name` as `gr_name`, `".$this->pr."values`.*
		FROM `".$this->pr."attrs`
		INNER JOIN `".$this->pr."templ_attrs` ON `".$this->pr."templ_attrs`.`attr`=`".$this->pr."attrs`.`id`
		INNER JOIN `".$this->pr."tvrs` ON `".$this->pr."templ_attrs`.`templ`='$item[templ]'
		LEFT JOIN `".$this->pr."attr_groups` ON `".$this->pr."attr_groups`.`id`=`group`
		RIGHT JOIN `".$this->pr."values` ON (`".$this->pr."values`.`attr`=`".$this->pr."attrs`.`id` AND `".$this->pr."values`.`tvr`=`".$this->pr."tvrs`.`id`)
		WHERE `".$this->pr."tvrs`.`id`=$id ORDER BY `group`";
		$attrs=db_query($q);
		$this->setLog($q);
		if(count($attrs))
			foreach($attrs as $one){			
				$attr=array('id'=>$one['id'],'title'=>$one['title'],'val'=>$one['t'.$one['type']],'unit'=>$one['unit'],'group'=>$one['group'],'group_name'=>$one['gr_name'],'type'=>$one['type'],'opts'=>$one['options']);
				if($one['options']&1==1) $item['attrs_hid'][$one['name']]=$attr;else
				$item['attrs'][$one['name']]=$attr;
			}
			$q="SELECT `name`,`caption` FROM `".$this->pr."images` WHERE `id_tvr`='".$item['id']."' ORDER BY `index`";
			$imgs=db_query($q);
			$this->setLog($q);
			if(count($imgs))
				$item['imgs']=$imgs;
			return $item;
		}

	public function getCatFilters($id){ //невозможна фильтрация по date(4) и text(6)
		$templ=$this->nodes[$id]['template'];	//участвуют только товары, которые count>0
		if($templ==0) return array();		
		$q="SELECT 
		`".$this->pr."attrs`.`id`,
		`".$this->pr."attrs`.`name`,
		`".$this->pr."attrs`.`title`,
		`".$this->pr."attrs`.`type`,
		`".$this->pr."attrs`.`unit`,
		`".$this->pr."values`.`attr`,
		`".$this->pr."values`.`t1`,
		`".$this->pr."values`.`t2`,
		`".$this->pr."values`.`t3`,
		`".$this->pr."values`.`t5`,
		`".$this->pr."values`.`t7`,
		CONCAT(`".$this->pr."attrs`.`type`,'_',
		`".$this->pr."values`.`attr`,'_',
		COALESCE(`".$this->pr."values`.`t1`,''),
		COALESCE(`".$this->pr."values`.`t2`,''),
		COALESCE(`".$this->pr."values`.`t3`,''),
		COALESCE(`".$this->pr."values`.`t5`,''),
		COALESCE(`".$this->pr."values`.`t7`,'')) as `salt`,
		`".$this->pr."templ_attrs`.`order`
		FROM `".$this->pr."attrs`
		INNER JOIN `".$this->pr."templ_attrs` ON `".$this->pr."templ_attrs`.`attr`=`".$this->pr."attrs`.`id`
		INNER JOIN `".$this->pr."tvrs` ON `".$this->pr."templ_attrs`.`templ`='$templ'
		LEFT JOIN `".$this->pr."values` ON (`".$this->pr."values`.`attr`=`".$this->pr."attrs`.`id` AND `".$this->pr."values`.`tvr`=`".$this->pr."tvrs`.`id`)
		WHERE (`".$this->pr."templ_attrs`.`order`>-1) AND 
		(`".$this->pr."tvrs`.`cat`=$id)  AND 
		(`".$this->pr."tvrs`.`price`>=0) AND
		(`".$this->pr."tvrs`.`count`>0) AND
		(`".$this->pr."tvrs`.`opts`&1=0)
		GROUP BY `salt`
		HAVING ((`t1` IS NOT NULL) OR (`t2` IS NOT NULL) OR (`t3` IS NOT NULL) OR (`t5` IS NOT NULL) OR (`t7` IS NOT NULL))
		ORDER BY `order` DESC,`t1`,`t2`,`t3`,`t5`,`t7`,`name` ASC";
		$attrs=db_query($q);
		$this->setLog($q);
		$res=array();
		$_vals=array();
		$_a=0;
		foreach($attrs as $one){
			if ($_a!=$one['id']){
				if($_a>0) sort($res[count($res)-1]['vals']);
				$res[]=array('name'=>$one['name'],'title'=>$one['title'],'type'=>$one['type'],'unit'=>$one['unit'],'vals'=>array());
				$_a=$one['id'];
			}
			$res[count($res)-1]['vals'][]=$one['t'.$one['type']];
		}
		if($_a>0) sort($res[count($res)-1]['vals']);
		return $res;
	}

	public function getCatPath($id,$asString=false){
		$c=array_reverse($this->getCatParent($id));
		if(!$asString) return $c;
		$str='';
		foreach($c as $one){
			$str.='/'.$one['link'];
		}
		return $str;
	}
	
	public function getCatPathA($alias,$asString=false){
		$cat=$this->getCatAlias($alias);
		return $this->getCatPath($cat['id'],$asString);
	}
	
	public function getCatAlias($alias){
		$res=false;
		foreach($this->nodes as $one)
			if($one['link']==$alias)
				$res=$one;
			return $res;
		}

		public function showHiddens($count_zero,$disabled=false){
			$this->show_count0=$count_zero;
			$this->show_disabled=$disabled;
		}

		private function __showHidden(){
			$res='';
		if($this->show_count0)				//товары с остатком=0
		$res=' `count`>=0 ';
		else 
			$res=' `count`>0 ';		
		if(!$this->show_disabled)					//отключенные товары
		$res.='AND (`opts`&1=0) ';
		// $this->__showHidden()=$res;		
		return $res;
	}

	
	
	public function getCatId($id){
		return $this->nodes[$id];
	}
	
	public function getCats($root=0){
		return $this->getCatChild($root);
	}	
	
	private function getCatChild($id,$level=0){
		$c=array();
		foreach($this->nodes as $one){
			if($one['root']==$id){
				$sub=$this->getCatChild($one['id'],($level+1));
				$one['level']=$level;
				if(count($sub)) $one['sub']=$sub;
				$c[$one['id']]=$one;
			}
		}
		return $c;
	}
	
	private function getCatParent($id){
		if($this->nodes[$id]['root']>0)
			return $c=array_merge(array(0=>$this->nodes[$id]),$this->getCatParent($this->nodes[$id]['root']));
		else
			return array(0=>$this->nodes[$id]);
	}	
	
	public function array_filter($array,$filters){//$filters=Array(Array("eq"=>'>',"key"=>'show',"val"=>'3','emptyskip'=>true));
	$res=Array();
	foreach($array as $one){
		$skip=false;
		foreach($filters as $fil){
			if($fil[emptyskip] && $one[$fil[key]]=='') continue; // игнорировать пустое значение
			switch($fil[eq]){
				case '': 
				case '=': if ($one[$fil[key]]!=$fil[val]) $skip=true; break; 
				case '>': if ($one[$fil[key]]<=$fil[val]) $skip=true; break; 
				case '>=': if ($one[$fil[key]]<$fil[val]) $skip=true; break; 
				case '<': if ($one[$fil[key]]>=$fil[val]) $skip=true; break; 
				case '<=': if ($one[$fil[key]]>$fil[val]) $skip=true; break; 
				case '!=':	
				case '<>': if ($one[$fil[key]]==$fil[val]) $skip=true; break;		
				case 'in': if (!in_array($one[$fil[key]],$fil[val])) $skip=true; break;	
				case '!in': if (in_array($one[$fil[key]],$fil[val])) $skip=true; break;	
			}
		}
		if ($skip) continue;
		array_push($res,$one);
	}
	return $res;
}

function __construct($pr='mcat_') {
	$this->showHiddens(false,false);		
	$this->pr=$pr;
	$q="SELECT `".$this->pr."nodes`.*, count(`".$this->pr."tvrs`.`id`) as `cnt`
	FROM `".$this->pr."tvrs`
	RIGHT JOIN `".$this->pr."nodes` ON `".$this->pr."nodes`.`id`=`".$this->pr."tvrs`.`cat`
	WHERE `".$this->pr."nodes`.`root`>=0
	GROUP BY `".$this->pr."nodes`.`name`
	ORDER BY `".$this->pr."nodes`.`root` ASC, `".$this->pr."nodes`.`fcomment` ASC;";

	$res=db_query($q);
	$this->setLog($q);




    if(count($res))
		foreach($res as $one)
			$this->nodes[$one['id']]=$one;
		if($this->nodes)
		foreach($this->nodes as &$one)
			$one['path']=$this->getCatPath($one['id'],1);			
		return $this;
	}
	function __destruct() {
	}
	static public function array_values_key($arr,$key){
		$res=array();
		foreach($arr as $one) $res[]=$one[$key];
		return $res;
	}
	
	static public function translit($str){
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '',	  'ы' => 'y',   'ъ' => '',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',        
			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',' ' => '-',
		);
		$str = strtolower(strtr($str, $converter));
		// заменям все ненужное нам на "-"
		$str = preg_replace('~[^-a-z0-9_\s]+~u', '', $str);
		$str = trim($str, "-");

		return $str;
	}
}

?>