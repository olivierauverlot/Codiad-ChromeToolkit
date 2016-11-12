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
    
    echo "<script>
    var controller='" . $_GET['controller'] . "';
    var projectName='" . $_GET['projectName'] . "';
    </script>";
 	?>
 	
    <form>
    	<label>Scan this QR code with your mobile device</label>
    	<div id='qrcode'></div>
    	<button class="btn-left">Close</button>
    </form>
    
    <script>
    	var url_ctrl = controller + '?' + $.param({
    		'action':'deployToMobile',
    		'projectName': projectName
    	});  
    	$.get(url_ctrl,function(data) {
    		response = JSON.parse(data);
    		if (response.status == 'success') {
    			$('#qrcode').append(response.data["message"]);
    		}
    	});
    </script>
    