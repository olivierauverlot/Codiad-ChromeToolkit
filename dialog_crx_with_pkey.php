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
	
<form>
	<label>User key pair</label>
	<textarea rows='10' cols='40' name='pkey' id='pkey' autocomplete='off' autofocus='autofocus'></textarea>
	Copy/Paste your key pair in the input field.<br>
	The information are not saved on the server<br> 
	and are only used to sign your CRX package.<br>	 
	<button class="btn-left">Build</button>
	<button class="btn-right" onclick="codiad.modal.unload();return false;">Cancel</button>
</form>