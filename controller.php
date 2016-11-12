<?php

    /*
    *  Copyright (c) Codiad & Olivier Auverlot, distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */


    require_once('../../common.php');
    require_once('class.chrome.php');

    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    $chrome = new Chrome();

    //////////////////////////////////////////////////////////////////
    // Create new project
    //////////////////////////////////////////////////////////////////

	switch($_GET['action']) {
		case 'newProject':
    		$chrome->projectName = trim($_GET['projectName']);
        	$chrome->projectType = $_GET['projectType'];
        	$chrome->CreateNewProject();
        	break;
        case 'addChromeManifest':
        	$chrome->projectName = trim($_GET['projectName']);
        	$chrome->addChromeManifest();
        	break;
        case 'buildSelfSignedCRX':
        	$chrome->projectName = trim($_GET['projectName']);
        	$chrome->buildSelfSignedCRX();
        	break;
        case 'buildUserSignedSignedCRX':
        	$chrome->projectName = trim($_GET['projectName']);
        	$chrome->buildUserSignedSignedCRX($_GET['pkey']);
        	break;
        case 'deployToMobile':
        	$chrome->projectName = $_GET['projectName'];
        	$chrome->deployToMobile();
        	break;
    }

?>