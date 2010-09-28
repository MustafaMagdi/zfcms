var pageTree;


function semiSortableTree(obj, orderUrl) {	
	
	this.selected = null;
	this.obj = $(obj);
	
	this.obj.sortable({
		distance: 10,
		connectWith: '.catList',
		helper: 'clone'
	}).disableSelection();
	
	this.obj.bind( "sortupdate", function(event, ui) {		
		var ul = ui.item.parent();
		var pid = ul.attr('rel');
		var order = ul.sortable("serialize");
		order = order + '&pid=' + pid;
		$.getJSON(orderUrl, order, function(data) {
		  if (data.result == false) alert(data.msg);
		});
	});
	
	
	this.obj.find('a.tgler').live('click', function(evt){
		evt.stopPropagation();
		var ul = $(this).parent().children('ul');		
		
		  if (!ul.get(0)) {
			var li = $(this).parent();
			var url = $(this).attr('href');	
			var	t = obj;			
			$.get(url, function(data) {
				li.append(data);				
				$(t).sortable("destroy");
				$(t).sortable({
					distance: 10,
					connectWith: '.catList'
				}).disableSelection();
			});
		  } else {
			ul.toggle();
		  }
		
		$(this).toggleClass('active');
		return false;
	});
	
	tree = this;
	
	this.obj.find('a.page').live('click', {sc: tree}, function(event) {
	  event.stopPropagation();	  
	  if (!$(this).hasClass('selected')) {
		event.data.sc.selected = $(this);
		event.data.sc.obj.find('a.page').removeClass('selected');
	  }
	  $(this).toggleClass('selected');
	  return false;
	});	
	
	this.obj.find('a.page').live('dblclick', {sc: tree}, function(event) {
	  event.stopPropagation();	  
	  if (!$(this).hasClass('selected')) $(this).click();
	  var url = $(this).attr('href');
	  $.getJSON(url, function(data) {
		  if (data.result == false) {
				alert(data.msg);
			} else {
				fillForm('#Page_Form_Edit', data.page);
			}
		});
	  return false;
	});	
	
}

function fillForm(form, vals) {
	form = $(form);
	for (var t in vals) {
		if (form.has('#' + t)) {
			$('#' + t).val(vals[t]);
		}
	}
	
}

function setPageToolbar(obj) {
	obj = $(obj);
	$('#pageAdd').click(function(event){
		event.stopPropagation();
		var pid = 0;			
		
		$('#fpeTitle').val('Новая страница');
		$('#fpeSubtitle').val('');
		$('#fpeIscat').val(0);		
		$('#fpeRang').val('-1');
		$('#fpeShortcut').val('');
		$('#fpeTags').val('');		
		$('#fpeText').tinymce().setContent('');
		$('#fpeText').val('');
		if (pageTree.selected != null) {
			var li = pageTree.selected.parent();
			if (li.hasClass('cat')) {
				pid = pageTree.selected.attr('rel');
			} else {
				pid = li.parent().attr('rel');				
			}			
		}
		$('#fpePid').val(pid);
		return false;
	});
	$('#pageEdit').click(function(event){
		event.stopPropagation();
		if (pageTree.selected != null) {
			pageTree.selected.dblclick();
		} else {
			alert('Нужно сначала выбрать страницу для редактирования');
		}
		return false;
	});
	$('#pageMove').click(function(event){
		event.stopPropagation();
		if (pageTree.selected != null) {
			var href = pageTree.selected.attr('href');
		} else {
			alert('Нужно сначала выбрать страницу для переноса');
		}
		return false;
	});
	$('#pageDelete').click(function(event){
		event.stopPropagation();
		if (pageTree.selected != null) {
			if (confirm('Вы действительно хотите удалить страницу "' + pageTree.selected.text() + '"?')) {
				var id = pageTree.selected.attr('rel');
				$.getJSON('/page/admin/delete/', {id: id}, function(data) {
				  if (data.result == false) {
					alert(data.msg);
					} else {
					$('#page_' + id).remove();
				  }
				});
			}
			
		} else {
			alert('Нужно сначала выбрать страницу для переноса');
		}
		return false;
	});	
}

function wysiwygAutoHeight() {
	var win = $(window).height();   // viewport
	var ah = win - 290;	
	$('#fpeText').height(ah);
}

function erroredInput(el) {
	el = $(el);
	el.addClass('error');
	el.focus(function () {$(this).removeClass('error')});
}

function savePage() {	
	var form = $('#Page_Form_Edit');
	var inputs = form.serializeObject();
	var errored = 0;
	if (inputs['fpeTitle'].length < 3) {
		erroredInput('#fpeTitle');
		errored = 1;
		alert('Заголовок должен быть длиннее 3 символов');
	}
	if (inputs['fpeTitle'] == 'Новая страница') {
		erroredInput('#fpeTitle');
		errored = 1;
		alert('Придумайте другое название для статьи');
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
					if ($('#Page_Form_Edit').has('#' + t)) {						
						erroredInput('#' + t);
					}
				}
				if (data.msg != '') alert(data.msg);				
			} else {
				fillForm('#Page_Form_Edit', data.page);
				if (data.append) {
					$('#pagechilds_' + data.page.fpePid).append(data.li);
				} else {
					$('#page_' + data.page.fpeId).replaceWith(data.li);
				}
				pageTree.obj.sortable("destroy");
				pageTree.obj.sortable({
					distance: 5,
					connectWith: '.catList'
				}).disableSelection();
				pageTree.obj.find('a.page').removeClass('selected');
				$('#page_' + data.page.fpeId).children('a.page').addClass('selected');
			}
		  }
		});
	}
	return false;
}

function setWYSIWYG() {		
	$('#fpeText').tinymce({		
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
		ifrond_image_url: "/cp/image/index/pmodule/page/",
		ifrond_file_url: "/cp/file/index/pmodule/page/",
		ifrond_seo_url: "/page/admin/seo/",
		setup : function(ed) {
			ed.addButton('ifrondsave', {
				title : 'Сохранить',			
				image : '/ifrond/css/images/mce/save.png',
				onclick : function() {
					tinyMCE.triggerSave();
					savePage();
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
			ed.addButton('ifrondseo', {
				title : 'SEO заголовки',			
				image : '/ifrond/css/images/mce/seo.png',
				onclick : function() {
					var id = ed.editorId;
					var form = ed.formElement;	
					$(form).find('#fpeId').val();
					var seoWin = $('#ifrondMCESeo').get(0);
					//console.log($('#ifrondMCESeo'));
					if (!seoWin) {
						var seoWin = $('<div id="ifrondMCESeo">loading...</div>');					
						seoWin.load(ed.settings.ifrond_seo_url).dialog({
							height: 350,
							width: 550,
							modal: false,
							title: 'SEO заголовки',
							close: function(event, ui) {
								$(this).dialog( "destroy" );
								$('#ifrondMCESeo').remove();
							}
							});				
					} else {
						if (!$('#ifrondMCESeo').dialog( "isOpen" )) $('#ifrondMCESeo').dialog('open');
					}
				}
			});
			
		}
	});
	
	wysiwygAutoHeight();
	
	$(window).resize(function(){wysiwygAutoHeight();});	
	
}

function setFormCallback() {
	$('#Page_Form_Edit').submit(savePage);
}


jQuery(function($) {	
	pageTree = new semiSortableTree('#pageTree ul', '/page/admin/rang/');	
	setPageToolbar('#pageToolbar');
	setWYSIWYG();
	setAutoHeight();	
	setFormCallback();	
});