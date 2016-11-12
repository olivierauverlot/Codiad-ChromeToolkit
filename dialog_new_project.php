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
	<label><span class="icon-list-add big-icon"></span>Chrome Toolkit</label>
    <hr>
	<label>Project Name</label>
    <input name="projectName" id="projectName" autocomplete="off" autofocus='autofocus'> 
    <label>Project type</label>
    <select name="projectType" id="projectType">
    	<optgroup label='Chrome apps'>
       		<option value="jsChromeOSApp">Javascript Chrome app</option>
       		<option value="jsChromeExt">Javascript Chrome Extension</option> 
    	</optgroup>
		<optgroup label="Web apps">
			<option value="jsWebApp">Javascript web app</option>
			<option value="jsWebAppMDL">Javascript web app (Using Material Design Lite)</option>
			<option value="jsWebAppMaterialize">Javascript web app (Using Materialize)</option>
  		</optgroup>
    </select>
    <button class="btn-left">Create</button>
    <button class="btn-right" onclick="codiad.modal.unload();return false;">Cancel</button>
</form>
