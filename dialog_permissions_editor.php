<?php
    /*
    *  Copyright (c) Codiad & Olivier Auverlot, distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */
    require_once('../../common.php');
    
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////
    
    checkSession();
?>

<form id='form_permissions'>
	<label>Chrome Permissions Editor</label>
	<hr>
		<select id="permCategories" onChange="codiad.chrome_permissions.permSelectAppType()">
    		<option value="permApp" default>Application</option>
    		<option value="permExt">Extension</option>
    	</select>
	<hr>
	<div id='permList'></div>
	<hr>
	<button class="btn-left">Insert</button>
    <button class="btn-right" onclick="codiad.modal.unload();return false;">Cancel</button>
</form>

<script>
	codiad.chrome_permissions = {
		permissions: [ 
			["activeTab",true,false],["alarms",true,true],['audio',false,true],
			['audioCapture',false,true],["background",true,true],["bookmarks",true,false],
			["browser",false,true],["browsingData",true,false],["certificateProvider",true,true],
			["clipboard",false,true],["clipboardRead",true,true],["clipboardWrite",true,true],
			["contentSettings",true,false],["contextMenus",true,true],["cookies",true,false],
			["debugger",true,false],["declarativeContent",true,false],["declarativeWebRequest",true,false],
			["desktopCapture",true,true],['diagnostics',false,true],["displaySource",true,true],
			["dns",true,true],["documentScan",true,true],["downloads",true,false],
			["enterprise.deviceAttributes",true,true],["enterprise.platformKeys",true,false],["experimental",true,true],
			["fileBrowserHandler",true,true],["fileSystem",false,true],["fileSystemProvider",true,true],
			["fontSettings",true,false],["gcm",true,true],["geolocation",true,true],
			["hid",false,true],["history",true,false],["identity",true,true],
			["idle",true,true],["idltest",true,false],["management",true,false],
			["mdns",false,true],["mediaGalleries",false,true],["nativeMessaging",true,true],
			["networking.config",true,true],["notificationProvider",true,true],["notifications",true,true],
			["pageCapture",true,false],["platformKeys",true,true],["pointerLock",false,true],
			["power",true,true],["printerProvider",true,true],["privacy",true,false],
			["processes",true,false],["proxy",true,true],["serial",false,true],
			["sessions",true,false],["signedInDevices",true,true],["socket",false,true],
			["storage",true,true],["syncFileSystem",false,true],["system.cpu",true,true],
			["system.display",true,true],["system.memory",true,true],["system.storage",true,true],
			["tabCapture",true,false],["tabs",true,false],["topSites",true,false],
			["tts",true,true],["ttsEngine",true,false],["unlimitedStorage",true,false],
			["vpnProvider",true,false],["wallpaper",true,false],["webNavigation",true,false],
			["webRequest",true,false],["webRequestBlocking",true,false]
		],
		permSelectAppType: function() {
			var a = false
			var e = false;
			var c = 1;
			var s = document.getElementById("permCategories");
			var divperms = document.getElementById("permList");
			if(s.options[s.selectedIndex].value == 'permApp') {
				a = true;
			} else {
				e = true;
			}
			var table = document.createElement('table');
			var row = document.createElement('tr');
			for(let v of this.permissions) {
				if(v[1] == e || v[2] == a) {
					var col = document.createElement('td');
					var checkbox = document.createElement("input");
					checkbox.type = "checkbox";
					checkbox.value = v[0];
					col.appendChild(checkbox);
					row.appendChild(col);
					col_label = document.createElement('td');
					var label = document.createElement('label')
					label.appendChild(document.createTextNode(v[0]));
					col_label.appendChild(label);
					row.appendChild(col_label);
					if(c++ == 3) {
						table.appendChild(row);	
						c = 1;
						row = document.createElement('tr');
					}
				}
			}
			document.getElementById('permList').innerHTML = '';
			divperms.appendChild(table);
		}
	};

	codiad.chrome_permissions.permSelectAppType()
</script>