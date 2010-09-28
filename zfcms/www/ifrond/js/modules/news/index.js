function fillForm(form, vals) {
	form = $(form);
	for (var t in vals) {
		if (form.has('#' + t)) {
			$('#' + t).val(vals[t]);
		}
	}
	
}

function setNewsToolbar(obj) {	
	$('#newsAdd').click(function(event){
		event.stopPropagation();
		var pid = 0;			
		
		$('#fneTitle').val('Новая новость');
		$('#fneSubtitle').val('');
		$('#fnePid').val(0);		
		$('#fneDate').val('-1');
		$('#fneTime').val('');
		$('#fneTags').val('');		
		$('#fneText').tinymce().setContent('');
		$('#fneText').val('');	
		$('#fnfPid').val(-1);	
		return false;
	});
	
	$('#newsEdit').click(function(event){
		event.stopPropagation();
		var selected = $("#cpNewses li.ui-selected").get(0);		
		if (selected) {
			var link = $('.ttl a.ajax', selected);
			link.click();
		} else {
			alert('Нужно сначала выбрать новость для редактирования');
		}
		return false;
	});
	
	$('#newsDelete').click(function(event){
		event.stopPropagation();
		var selected = $("#cpNewses li.ui-selected").get(0);		
		if (selected) {			
			var id = $(selected).attr('rel');
			$.getJSON('/news/admin/delete/', {id: id}, function(data) {
			  if (data.result == false) {
				  alert(data.msg);
				} else {
					$('#newsLi_' + id).remove();
			  }
			});
		} else {
			alert('Нужно сначала выбрать новость для редактирования');
		}
		return false;
	});
	
	$('#newsFilter').click(function(evt){
		evt.stopPropagation();
		var filterWin = $('#ifrondNewsFilter').get(0);
		if (!filterWin) {
			filterWin = $('<div id="ifrondNewsFilter">loading...</div>');
			filterWin.load('/news/admin/filter/').dialog({
				height: 170,
				width: 350,
				title: 'Фильтр новостей',
				modal: false,
				zIndex: 300100,
				close: function(event, ui) {
					$(this).dialog( "destroy" );
					$('#ifrondNewsFilter').remove();
				}
			});	
		} else {
			if (!$('#ifrondNewsFilter').dialog( "isOpen" )) $('#ifrondNewsFilter').dialog('open');
		}
		return false;
	});
}

function wysiwygNewsAutoHeight() {	
	var win = $(window).height();   // viewport
	var ah = win - 420;	
	$('#fneText').height(ah);	
}

function erroredInput(el) {
	el = $(el);
	el.addClass('error');
	el.focus(function () {$(this).removeClass('error')});
}

function saveNews() {	
	var form = $('#News_Form_Edit');
	var inputs = form.serializeObject();
	var errored = 0;
	if (inputs['fneTitle'].length < 3) {
		erroredInput('#fpeTitle');
		errored = 1;
		alert('Заголовок должен быть длиннее 3 символов');
	}
	if (inputs['fneTitle'] == 'Новая новость') {
		erroredInput('#fneTitle');
		errored = 1;
		alert('Придумайте другое название для новости');
	}
	if (errored == 0) {
		inputs = form.serialize();
		var url = form.attr('action');
		$.ajax({
		  url: url,
		  data: inputs,
		  dataType: "json",
		  type: "POST",
		  success: function(data) {
			if (data.result == false) {
				for (var t in data.errors) {
					if ($('#News_Form_Edit').has('#' + t)) {						
						erroredInput('#' + t);
					}
				}
				if (data.msg != '') alert(data.msg);				
			} else {
				fillForm('#News_Form_Edit', data.page);				
			}
		  }
		});
	}
	return false;
}

function setWYSIWYG() {	
	
	wysiwygNewsAutoHeight();
	
	$('#fneText').tinymce({		
		document_base_url : "/",		
		relative_urls : false,
		script_url : '/ifrond/js/tiny_mce/tiny_mce.js',
		language: 'ru',
		skin : "o2k7",
		// General options
		theme : "advanced",		
		plugins : "style,table,advimage,advlink,paste,inlinepopups,media,fullscreen",
		// Theme options
		theme_advanced_buttons1 : "ifrondsave,|,undo,redo,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,hr,image,media,|,removeformat,cleanup,code,fullscreen",		
		theme_advanced_buttons2 : "pastetext,pasteword,styleselect,formatselect,|,tablecontrols,|,ifrondimage,ifrondfile,ifrondseo",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		dialog_type : "modal",
		content_css : "/ifrond/css/wisiwyg.css",
		convert_fonts_to_spans : true,
		cleanup_on_startup : true,
		cleanup: true,		
		ifrond_image_url: "/cp/image/index/pmodule/news/",
		ifrond_file_url: "/cp/file/index/pmodule/news/",		
		setup : function(ed) {
			ed.addButton('ifrondsave', {
				title : 'Сохранить',			
				image : '/ifrond/css/images/mce/save.png',
				onclick : function() {
					tinyMCE.triggerSave();
					saveNews();
				}
			});
			ed.addButton('ifrondimage', {
				title : 'Вставить изображение',			
				image : '/ifrond/css/images/mce/image.png',
				onclick : function() {
					var id = ed.editorId;
					var form = ed.formElement;	
					var imageWin = $('#ifrondMCEImage').get(0);
					//console.log($('#ifrondMCEImage'));
					if (!imageWin) {
						imageWin = $('<div id="ifrondMCEImage">loading...</div>');
						imageWin.load(ed.settings.ifrond_image_url).dialog({
							height: 350,
							width: 550,
							modal: false,
							zIndex: 300100,
							title: '',
							close: function(event, ui) {
								$(this).dialog( "destroy" );
								$('#ifrondMCEImage').remove();
							}
						});	
					} else {
						if (!$('#ifrondMCEImage').dialog( "isOpen" )) $('#ifrondMCEImage').dialog('open');
					}
								
					
				}
			});
			ed.addButton('ifrondfile', {
				title : 'Загрузить файл',			
				image : '/ifrond/css/images/mce/file.png',
				onclick : function() {
					var id = ed.editorId;
					var form = ed.formElement;	
					var fileWin = $('#ifrondMCEFile').get(0);
					if (!fileWin) {
						fileWin = $('<div id="ifrondMCEFile">loading...</div>');
						fileWin.load(ed.settings.ifrond_file_url).dialog({
							height: 350,
							width: 550,
							modal: false,
							zIndex: 300100,
							title: '',
							close: function(event, ui) {
								$(this).dialog( "destroy" );
								$('#ifrondMCEFile').remove();
							}
						});	
					} else {
						if (!$('#ifrondMCEFile').dialog( "isOpen" )) $('#ifrondMCEFile').dialog('open');
					}
				}
			});	
			
		}
	});
	
	
	
	$(window).resize(function(){wysiwygNewsAutoHeight();});	
	
}

function setFormCallback() {
	$('#News_Form_Edit').submit(saveNews);
}

