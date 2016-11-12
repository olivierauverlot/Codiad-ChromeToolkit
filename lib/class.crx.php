<?php
    /*
    *  Copyright (c) Codiad & Olivier Auverlot, distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */
class CrxBuilder {
    private $projectName;
	private $buildFolder = 'Build';
	public $buildPath;	
	public $pkey;
	public $privateKey,$publicKey;
	
	function __construct($_projectName,$_pkey) {
		$this->projectName = $_projectName;
		$this->pkey = $_pkey;
		$this->buildPath = WORKSPACE . '/' . $this->projectName . '/' . $this->buildFolder;		
    } 
    
    public function getCRX() {
    	$this->createCRX();
    }
    
	public function createCRX() {
    	$rootPath = realpath(WORKSPACE . '/' . $this->projectName);

		// extract the private key and the public key
    	$this->privateKey = openssl_pkey_get_private($this->pkey);
		$this->publicKey = openssl_pkey_get_details($this->privateKey)['key'];
		
		// create the zip file
		$zipname = tempnam("tmp", "zip");
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);
			
		// build archive content
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($rootPath),
		    RecursiveIteratorIterator::LEAVES_ONLY
		);
		
		foreach ($files as $name => $file) {
			// Skip directories (they would be added automatically)
			// the build directory doesn't be included
			if (!$file->isDir() && strpos($file, $this->buildPath) === false) {
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($rootPath) + 1);
				$zip->addFile($filePath, $relativePath);
			}
		}
		$zip->close();
		
    	# make a SHA1 signature using our private key
		openssl_sign(file_get_contents($zipname), $signature, $this->privateKey, OPENSSL_ALGO_SHA1);
		$this->publicKey = trim(explode('-----',$this->publicKey)[2]);
		$this->publicKey = base64_decode($this->publicKey);
		
		# Create the crx file
		$extname = tempnam("tmp", "ext");
		$fh = fopen($extname, 'wb');
		fwrite($fh, 'Cr24');                             
		fwrite($fh, pack('V', 2));                       	
		fwrite($fh, pack('V', strlen($this->publicKey))); 
		fwrite($fh, pack('V', strlen($signature)));
		fwrite($fh, $this->publicKey);
		fwrite($fh, $signature); 
		fwrite($fh, file_get_contents($zipname));
		fclose($fh);

		// copy the crx in the build folder
		copy($extname,($this->buildPath . '/' . $this->projectName . '.crx'));
		
		// remove the temporary files 
		unlink($extname);
		unlink($zipname); 	
    }
}

class CrxBuilderSelfSigned extends CrxBuilder {
	private $keyPath;

	function __construct($_projectName) {
		parent::__construct($_projectName,'');
		$this->keyPath = $this->buildPath . '/' . 'pkey.pem';
		$this->genAutosignedKeys();
    }
    
    private function genAutosignedKeys() {
		if (is_dir($this->buildPath) == false) {
			mkdir($this->buildPath);
    	}
    	if(!file_exists($this->keyPath)) {
			// Create the keypair and save the key to private.key file.
			$privateKey = openssl_pkey_new();
			openssl_pkey_export_to_file($privateKey, $this->keyPath);	
			file_put_contents(($this->buildPath . '/README'), "This directory contains a development key pair that must used for tests only.\n\nBe carreful to not distribute this key pair with your application.");
		}
    }
    
    public function getCRX() {
    	// read the keys from the pkey.pem file
		$this->pkey = file_get_contents($this->keyPath);
		$this->createCRX();
    }
}

?>
