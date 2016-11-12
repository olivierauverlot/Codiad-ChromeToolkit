/*
 * Copyright (c) Codiad & Olivier Auverlot, distributed
 * as-is and without warranty under the MIT License.
 * See http://opensource.org/licenses/MIT for more information.
 * This information must remain intact.
 */
 
(function(global, $){
    
    // Define core
    var codiad = global.codiad,
        scripts= document.getElementsByTagName('script'),
        path = scripts[scripts.length-1].src.split('?')[0],
        curpath = path.split('/').slice(0, -1).join('/')+'/';

    // Instantiates plugin
    $(function() {    
        codiad.chrome.init();
    });

    codiad.chrome = { 
        path: curpath,
        init: function() {
            $('.project-list-title').append('<a id="project-chrome" class="icon-list-add icon" ></a>');
            $('#project-chrome').click(codiad.chrome.create_project);
            amplify.subscribe('context-menu.onShow', function(obj){
                if ($(obj.e.target).hasClass('directory')) {
                    $('#context-menu').append('<hr class="directory-only chromeToolkit">');
                    $('#context-menu').append('<a class="directory-only chromeToolkit" onclick="codiad.chrome.addChromeManifest();"><span class="icon-doc-text"></span>Add Chrome Manifest</a>');
                    $('#context-menu').append('<a class="directory-only chromeToolkit" onclick="codiad.chrome.permissionsEditor();"><span class="icon-doc-text"></span>Chrome Permissions Editor</a>');
                    $('#context-menu').append('<a class="directory-only chromeToolkit" onclick="codiad.chrome.buildSelfSignedCRX();"><span class="icon-export"></span>Build self signed CRX Package</a>');
                    $('#context-menu').append('<a class="directory-only chromeToolkit" onclick="codiad.chrome.buildUserSignedCRX();"><span class="icon-export"></span>Build CRX Package</a>');
                    $('#context-menu').append('<a class="directory-only chromeToolkit" onclick="codiad.chrome.deployToMobile();"><span class="icon-phone"></span>Deploy to mobile</a>');
                }
            });
            amplify.subscribe("context-menu.onHide", function(){
                $('.chromeToolkit').remove();
            });
        },
        isEnabledCRXDownload: function () {
            var setting = localStorage.getItem('codiad.plugin.chrome.enableCRXDownload');
            return (setting === "true")
        },
        create_project: function() {
            // Select the type of the Chrome project
            var controller = curpath + 'controller.php';
            codiad.modal.load(400, curpath + 'dialog_new_project.php' );
            $('#modal-content form').live('submit', function(e) {
                e.preventDefault();
            	var projectName = $('#modal-content form input[name="projectName"]').val();
            	var projectType = $('#modal-content form #projectType option:selected').val();
            	$.get(controller + '?' + $.param({ 'action': 'newProject', 'projectName': projectName , 'projectType': projectType })  , function(data) {
                	createResponse = codiad.jsend.parse(data);
                    if (createResponse != 'error') {
                    	codiad.message.success(createResponse.message);
                        codiad.filemanager.rescan(projectName);
                        codiad.project.open(projectName);
                    } else {
                    	codiad.message.error(createResponse);
                    }
            		codiad.modal.unload();
            	});
            });
        },
        addChromeManifest: function() {
        	var _this = this;
         	var controller = curpath + 'controller.php';
        	$.get(controller + '?' + $.param({ 'action': 'addChromeManifest', 'projectName': codiad.project.getCurrent() })  , function(data) {
            	createResponse = codiad.jsend.parse(data);
                if (createResponse != 'error') {
                    codiad.message.success(createResponse.message);
                    codiad.filemanager.rescan(codiad.project.getCurrent());
                } else {
            		codiad.message.error(createResponse);
                }
            });
        },
        downloadCRXPath: function() {
         	return codiad.project.getCurrent() + '/Build/' + codiad.project.getCurrent() + '.crx';
         },
         buildSelfSignedCRX: function() {
         	var _this = this;
         	var controller = curpath + 'controller.php';
         	$.get(controller + '?' + $.param({ 'action': 'buildSelfSignedCRX', 'projectName': codiad.project.getCurrent() })  , function(data) {
            	createResponse = codiad.jsend.parse(data);
                if (createResponse != 'error') {
                    codiad.message.success(createResponse.message);
                    codiad.filemanager.rescan(codiad.project.getCurrent());
                    if(_this.isEnabledCRXDownload()) {
                    	codiad.filemanager.download(_this.downloadCRXPath());
                    }
                } else {
            		codiad.message.error(createResponse);
                }
            });
         },
         buildUserSignedCRX: function() {
  			var _this = this;
         	var controller = curpath + 'controller.php';
            codiad.modal.load(400, curpath + 'dialog_crx_with_pkey.php');
            $('#modal-content form').live('submit', function(e) {
                e.preventDefault();
                var pkey = $('#modal-content form textarea[name="pkey"]').val();
				$.get(controller + '?' + $.param({ 'action': 'buildUserSignedSignedCRX', 'projectName': codiad.project.getCurrent(), 'pkey': pkey})  , function(data) {
                	createResponse = codiad.jsend.parse(data);
                    if (createResponse != 'error') {
                    	codiad.message.success(createResponse.message);
                        codiad.filemanager.rescan(codiad.project.getCurrent());
                        if(_this.isEnabledCRXDownload()) {
                        	codiad.filemanager.download(_this.downloadCRXPath());
                        }
                    } else {
                    	codiad.message.error(createResponse);
                    }
            		codiad.modal.unload();
            	});
            });
         },
         permissionsEditor: function() {
         	var _this = this;
         	var controller = curpath + 'controller.php';
            codiad.modal.load(600, curpath + 'dialog_permissions_editor.php');
			$('#modal-content form').live('submit', function(e) {
                e.preventDefault();
                var permissions = [];
                $("#form_permissions input:checkbox:checked").each(function() {
    				permissions.push('"' + this.value + '"'); 
				});
            	codiad.modal.unload();
            	codiad.active.insertText('"permissions": [ ' + permissions.join() + ' ]');
            });
         },
         deployToMobile: function() {
         	var controller = curpath + 'controller.php';
            codiad.modal.load(400, curpath + 'dialog_deploy_to_mobile.php?controller=' + controller + '&projectName=' + codiad.project.getCurrent());
            $('#modal-content form').live('submit', function(e) {
            	e.preventDefault();
            	codiad.modal.unload();
            });
         }
    };

})(this, jQuery); 
