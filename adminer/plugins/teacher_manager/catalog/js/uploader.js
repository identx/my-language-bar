if(!XMLHttpRequest.prototype.sendAsBinary){
	XMLHttpRequest.prototype.sendAsBinary = function(datastr) {
		function byteValue(x){return x.charCodeAt(0)&0xff;}
		var ords=Array.prototype.map.call(datastr,byteValue);
		var ui8a=new Uint8Array(ords);
		this.send(ui8a.buffer);
	}
}
/**
 * Класс mgUploader.
 * @param ioptions Ассоциативный массив опций загрузки 
 * {
 *	"message_error"		:"Ошибка при загрузке изображений", 		// Основная ошибка
 * 	"form"				:"formImgUpload",							// ИД формы
 * 	"inputName"			:"imgLoader",								// Имя инпута
 * 	"dndName"			:"dndLoader",								// Имя drop зоны
 * 	"dndEnable"			:false										// Включить drop зону
 * 	"inputId"			:"imgLoader",								// ИД инпута
 * 	"handler"			:"/admin/handlers/h_upload.php",			// обработчик
 * 	"portion"			:1024*1024*2,								// размер чанка
 * 	"onProgress"		:progressConsole,							// функция прогресса
 * 	"onProgressGlobal"	:progressConsoleGlobal,						// функция общего прогресса
 * 	"onError"			:progressError,								// функция вывода ошибки
 * 	"supportTypes"		:["image/jpeg","image/png","image/gif"],	// поддерживаемые типы
 * 	"multiUpload"		:false,										// включить ли мультизагрузку
 * 	"onComplete"		:function(){},								// событие завершения загрузки
 * 	"onCompleteAll"		:function(){},								// событие полной загрузки
 * 	"onLoadStart"		:function(){},								// событие начала загрузки
 * 	"onLoadEnd"			:function(){},								// событие конца загрузки
 * }
 */
function FileUploader(ioptions){
	this.position=0;
	this.filesize=0;
	this.file=null;
	this.options=ioptions;
	this.stack=[];
	this.globalProgress=0;
	this.hash=this.options['inputId']+"_"+Math.random();
	if(typeof this.options['handler']=="undefined")
		return null;
	if(typeof this.options['onProgress']!="function")
		this.options['onProgress']=function(){};
	if(typeof this.options['onProgressGlobal']!="function")
		this.options['onProgressGlobal']=function(){};
	if(typeof this.options['onComplete']!="function")
		this.options['onComplete']=function(){};
	if(typeof this.options['onCompleteAll']!="function")
		this.options['onCompleteAll']=function(){};
	if(typeof this.options['onError']!="function")
		this.options['onError']=function(msg){alert(msg)};
	if(typeof this.options['supportTypes']!="object")
		this.options['supportTypes']=[];
	if(typeof this.options['multiUpload']!="boolean")
		this.options['multiUpload']=false;
	if(typeof this.options['onLoadStart']!="function")
		this.options['onLoadStart']=function(that){
			var e=document.getElementById(that.options['inputId']);
			if(e) e.setAttribute("disabled","disabled");
		};
	if(typeof this.options['onLoadEnd']!="function")
		this.options['onLoadEnd']=function(that){
			var e=document.getElementById(that.options['inputId']);
			if(e) e.removeAttribute("disabled");
		};

	this.CheckBrowser=function(){
		return (window.File&&window.FileReader&&window.FileList&&window.Blob)?true:false;
	}

	this.UploadPortion=function(from){
		var reader=new FileReader(),that=this,loadfrom=from,blob=null,xhrHttpTimeout=null;
		reader.onloadend=function(evt){
			if(evt.target.readyState==FileReader.DONE){
				var xhr=new XMLHttpRequest();
				xhr.open('POST',that.options['handler'],true);
				xhr.setRequestHeader("Content-Type","application/x-binary; charset=x-user-defined");
				xhr.setRequestHeader("Upload-Id",that.hash);
				xhr.setRequestHeader("Portion-From",from);
				xhr.setRequestHeader("Portion-Size",that.options['portion']);
				that.xhrHttpTimeout=setTimeout(function(){xhr.abort();},that.options['timeout']);
				xhr.upload.addEventListener("progress",function(evt){
					if(evt.lengthComputable){
						var percentComplete=Math.round((loadfrom+evt.loaded)*1000/that.filesize);percentComplete/=10;
						that.options['onProgress'](that,percentComplete);
					}
				},false);
				xhr.addEventListener("load",function(evt){
					clearTimeout(that.xhrHttpTimeout);
					if(evt.target.status!=200){
						that.options['onError'](evt.target.responseText);
						return;
					}
					that.position+=that.options['portion'];
					if(that.filesize>that.position){
						that.UploadPortion(that.position);
					} else {
						var gxhr=new XMLHttpRequest();
						gxhr.open('GET',that.options['handler']+'?action=done',true);
						gxhr.setRequestHeader("Upload-Id",that.hash);
						gxhr.addEventListener("load",function(evt){
							if(evt.target.status!=200){
								that.options['onError'](evt.target.responseText.toString());
								return;
							} else {
								// that.options['onError'](evt.target.responseText.toString());
								that.options['onComplete'](that);
								if(that.stack.length){
									that.options['onProgressGlobal'](that,(that.globalProgress-that.stack.length),that.globalProgress);
									that.Upload();
								} else {
									if(that.globalProgress>1)
										that.options['onProgressGlobal'](that,that.globalProgress,that.globalProgress);
									that.options['onCompleteAll'](that);
									that.options['onLoadEnd'](that);
								}
							}
						},false);
						gxhr.sendAsBinary('');
					}
				},false);
				xhr.addEventListener("error",function(evt){
					clearTimeout(that.xhrHttpTimeout);
					var gxhr= new XMLHttpRequest();
					gxhr.open('GET',that.options['handler']+'?action=abort',true);
					gxhr.setRequestHeader("Upload-Id",that.hash);
					gxhr.addEventListener("load",function(evt){
						if(evt.target.status!=200){
							that.options['onError'](evt.target.responseText);
							return;
						}
					},false);
					gxhr.sendAsBinary('');
					if(that.options['message_error']==undefined){
						that.options['onError']("There was an error attempting to upload the file.");
					} else {
						that.options['onError'](that.options['message_error']);
					}
				},false);
				xhr.addEventListener("abort",function(evt){
					clearTimeout(that.xhrHttpTimeout);
					that.UploadPortion(that.position);
				},false);
				xhr.sendAsBinary(evt.target.result);
			}
		};
		that.blob=null;
		if(this.file.slice){
			that.blob=this.file.slice(from,from+that.options['portion']);
		} else {
			if(this.file.webkitSlice){
				that.blob=this.file.webkitSlice(from,from+that.options['portion']);
			} else {
				if(this.file.mozSlice){
					that.blob=this.file.mozSlice(from,from+that.options['portion']);
				}
			}
		}
		reader.readAsBinaryString(that.blob);
	}

	this.typeSupport=function(type){
		for(var i=0,l=this.options['supportTypes'].length;i<l;i++){
			if(this.options['supportTypes'][i]==type){return true;}
		}
		return false;
	}

	this.Upload=function(){
		if(!this.stack.length){
			this.options['onError']("Очередь пуста");
			return false;
		}
		var f=this.stack.shift();
		this.position=0;
		this.filesize=f.size;
		this.file=f;
		this.hash=this.options['inputId']+"_"+Math.random();
		if(!this.file) return -1;;
		this.options['onProgress'](this,0);
		if(this.options['supportTypes'].length){
			if(!this.typeSupport(this.file.type)){
				this.options['onError']("Тип файла не поддерживается");
				return -1;
			}
		}
		this.UploadPortion(0);
	}

	if(this.CheckBrowser()){
		if(this.options['portion']==undefined) this.options['portion']=1048576;
		if(this.options['timeout']==undefined) this.options['timeout']=15000;
		var that=this;
		document.getElementById(this.options['inputName']).addEventListener('change',inputHandler, false);
		if (this.options['dndEnable'])
			document.getElementById(this.options['dndName']).addEventListener('drop',inputHandler, false);
	}
	function inputHandler(evt){
		var c = evt.target.files || evt.dataTransfer.files;
		for(var i=0,f;f=c[i];i++){
				that.stack.push(f);
				if(!that.options['multiUpload'])
					break;	}
		that.globalProgress=i;
		if(that.stack.length){
			that.options['onLoadStart'](that);
			that.Upload();
		}
	}
};