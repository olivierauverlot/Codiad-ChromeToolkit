<?php

    /*
    *  Copyright (c) Codiad & Olivier Auverlot, distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */

require_once('../../common.php');
require_once('./lib/phpqrcode.php');
require_once('./lib/class.crx.php');

class Chrome extends Common {

    public $projectName = '';
    public $projectType = '';

    public function __construct(){
    }

	public function addProject() {
		$this->projects = getJSON('projects.php');
        $this->projects[] = array("name"=>$this->projectName,"path"=>$this->projectName);
        saveJSON('projects.php',$this->projects);
	}

	public function initManifest($dest) {
		$data = file_get_contents($dest . '/manifest.json');
		$data = str_replace('{{projectName}}',$this->projectName,$data);
		file_put_contents($dest . '/manifest.json',$data);
	}
	
	public function initTemplate($dest) {
		$data = file_get_contents($dest . '/index.html');
		$data = str_replace('{{projectName}}',$this->projectName,$data);
		switch($this->projectType) {
			case 'jsWebApp':
				$data = str_replace('{{lib-css}}','',$data);
				break;
			case 'jsWebAppMDL':
				$code = '<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://code.getmdl.io/1.2.1/material.indigo-pink.min.css">
<script defer src="https://code.getmdl.io/1.2.1/material.min.js"></script>'; 
				$data = str_replace('{{lib-css}}',$code,$data);
				break;
			case 'jsWebAppMaterialize':
				$code = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/css/materialize.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/js/materialize.min.js"></script>';
				$data = str_replace('{{lib-css}}',$code,$data);
				break;
		}
		file_put_contents($dest . '/index.html',$data);
	}
	
	// Create a new project
    public function createNewProject() {
    	// openlog("ChromeToolkit", LOG_NDELAY, LOG_USER);
    	// syslog(LOG_NOTICE, "Something has happened");
		if($this->projectName != '') {
			$dest = WORKSPACE . '/' . $this->projectName;
			if(!file_exists($dest)) {
				shell_exec("mkdir " . $dest);
				switch ($this->projectType) {
					case 'jsChromeOSApp':
						shell_exec('cp -r ' . PLUGINS . '/Codiad-ChromeToolkit-master/templates/CrOSJSApp/* ' . $dest);
						break;
					case 'jsChromeExt':
						shell_exec('cp -r ' . PLUGINS . '/Codiad-ChromeToolkit-master/templates/CrJSExt/* ' . $dest);
						break;
					case 'jsWebApp':
					case 'jsWebAppMDL':
					case 'jsWebAppMaterialize':
					case 'jsWebAppBootstrap':
						shell_exec('cp -r ' . PLUGINS . '/Codiad-ChromeToolkit-master/templates/JSWebApp/* ' . $dest);
						break;
				}
				if($this->projectType == 'jsChromeOSApp' || $this->projectType == 'jsChromeExt') {
					$this->initManifest($dest);
				}
				$this->initTemplate($dest);
				$this->addProject();
				echo formatJSEND("success",array("message"=>"Project created"));
			} else {
				echo '{"status":"error","message":"Directory already exists"}';
			}
        } else {
            echo '{"status":"error","message":"No Project Name specified"}';
        }
        // closelog();
    }
    
    // Add a Chrome manifest in a existing project
    public function addChromeManifest() {
    	if($this->projectName != '') {
    		$source = PLUGINS . '/Codiad-ChromeToolkit-master/templates/CrOSJSApp/manifest.json';
			$dest = WORKSPACE . '/' . $this->projectName;
			$ret = copy($source,($dest . '/manifest.json'));
			if($ret) {
				$this->initManifest($dest);
				echo formatJSEND("success",array("message"=>"Manifest added"));
			} else {
				echo '{"status":"error","message":"Can\'t add manifest"}';
			}
    	}
    }
    
    // Build a self signed CRX
    public function buildSelfSignedCRX() {
    	if(file_exists(WORKSPACE . '/' . $this->projectName . '/manifest.json')) {
			$builder = new CrxBuilderSelfSigned($this->projectName);
			$builder->getCRX();
    		echo formatJSEND("success",array("message"=>"CRX package created"));
    	} else {
    		echo '{"status":"error","message":"No manifest found"}';
    	}
    }
    
    // Build a CRX with user pair key
    public function buildUserSignedSignedCRX($pkey) {
    	if(file_exists(WORKSPACE . '/' . $this->projectName . '/manifest.json')) {
    		if($pkey != '') {
				$builder = new CrxBuilder($this->projectName,$pkey);
				$builder->getCRX();
    			echo formatJSEND("success",array("message"=>"CRX package created"));
    		} else {
    			echo '{"status":"error","message":"No pair key specified"}';
    		}
    	} else {
    		echo '{"status":"error","message":"No manifest found"}';
    	}    	
    }
    
    // Generate QR Code to Deploy application on a mobile device
    public function deployToMobile() {
    	// build URL to the project
    	$url = WSURL . '/' . $this->projectName;
    	// build the QR Code
    	ob_start();
		QRcode::png($url);
		$png = ob_get_contents();
		ob_end_clean();
		// answer the JSON data
    	$data = "<img src='data:image/jpeg;base64," . base64_encode($png) . "' />";
    	echo formatJSEND("success",array("message"=>$data));
    }
}

?>