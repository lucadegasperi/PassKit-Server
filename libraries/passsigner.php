<?php

namespace PassKitServer\Libraries;

use File;
use Config;

class PassSignerDirectoryException extends \Exception {}

class PassSigner
{
	private $pass_url;
	private $certificate_url;
	private $certificate_password;
	private $output_url;
	private $compress_into_zip_file;
	private $temporary_directory;
	private $temporary_path;
	private $manifest_url;
	private $signature_url;
	
	public function __construct($pass_url, $certificate_url, $certificate_password, $output_url, $compress_into_zip_file = true)
	{
		$this->pass_url = $pass_url;
		$this->certificate_url = $certificate_url;
		$this->certificate_password = $certificate_password;
		$this->output_url = $output_url;
		$this->compress_into_zip_file = $compress_into_zip_file;
	}
	
	public function sign($force_clean_raw_pass = false)
	{
		// Validate that requested contents are not a signed and expanded pass archive.
		$this->validate_directory_as_unsigned_raw_pass($force_clean_raw_pass);
		
		$this->create_temporary_directory();
		
		$this->copy_pass_to_temporary_location();
		
		$this->clean_ds_store_files();
		
		$this->generate_json_manifest();
		
		$this->sign_manifest();
		
		$this->compress_pass_file();
		
	}
	
	private function validate_directory_as_unsigned_raw_pass($force_clean = false)
	{
		if($force_clean)
		{
			$this->force_clean_raw_pass();
		}
		
		$has_manifest = false;
		$has_signature = false;
		
		if(File::exists($this->pass_url . DS . 'manifest.json'))
		{
			$has_manifest = true;
		}
		
		if(File::exists($this->pass_url . DS . 'signature'))
		{
			$has_signature = true;
		}
		
		if($has_manifest || $has_signature)
		{
			// raise error
			throw new PassSignerDirectoryException();
		}
	}
	
	private function force_clean_raw_pass()
	{
		if(File::exists($this->pass_url . DS . 'manifest.json'))
		{
			File::delete($this->pass_url . DS . 'manifest.json');
		}
		
		if(File::exists($this->pass_url . DS . 'signature'))
		{
			File::delete($this->pass_url . DS . 'signature');
		}
	}
	
	private function create_temporary_directory()
	{
		$this->temporary_directory = Config::get('passkitserver::config.temporary_directory');
		
		$explosion = explode(DS, $this->pass_url);
		
		var_dump($explosion);
				
		$this->temporary_path = $this->temporary_directory . DS . $explosion[count($explosion)-1];
		
		echo 'creating temporary directory at: '.$this->temporary_path. '<br>';
		
		if(File::exists($this->temporary_path))
		{
			File::cleandir($this->temporary_path);
			echo 'temporary directory at: '.$this->temporary_path.' alreay existed, cleaning it.<br>';
		}
		else
		{
			File::mkdir($this->temporary_path);
		}
	}
	
	private function copy_pass_to_temporary_location()
	{
		File::cpdir($this->pass_url, $this->temporary_path);
		echo 'copying file from: '.$this->pass_url.' to '. $this->temporary_path .'<br>';
	}
	
	private function clean_ds_store_files()
	{
		$it = new \RecursiveDirectoryIterator($this->temporary_path);

		foreach(new \RecursiveIteratorIterator($it) as $file)
		{
			echo $file->getFileName().'<br>';
			if($file->getFilename() == '.DS_Store')
			{
				File::delete($file->getPathname());
			}
		}	
	}
	
	private function generate_json_manifest()
	{
		$it = new \RecursiveDirectoryIterator($this->temporary_path);
		
		$manifest = array();

		foreach(new \RecursiveIteratorIterator($it) as $file)
		{
			if(!$file->isDir())
			{
				$manifest[$file->getBasename()] = sha1_file($file->getPathname());
			}
		}
		
		$this->manifest_url = $this->temporary_path . DS . 'manifest.json';
		File::put($this->manifest_url, json_encode($manifest));
	}
	
	private function sign_manifest()
	{
		$p12_certificate = array();
		
		$flags = PKCS7_BINARY|PKCS7_DETACHED|PKCS7_NOATTR;
		
		$this->signature_url = $this->temporary_path . DS . 'signature';
		
		$ok = false;
		
		$ok = openssl_pkcs12_read(File::get($this->certificate_url), $p12_certificate, $this->certificate_password);
		
		if(!$ok)
		{
			echo 'error reading the certiticate';
		}
		
		$ok = openssl_pkcs7_sign($this->manifest_url, $this->signature_url, $p12_certificate['cert'], $p12_certificate['pkey'], array(), $flags);
		
		
		if(!$ok)
		{
			echo 'error signing the package';
		}
		
		
		$signature = File::get($this->signature_url);
		


		$pattern = "/.*?Content-Disposition: attachment; filename=\".*?\"(.*?)-----.*?/sm";
		preg_match_all($pattern, $signature, $match_result);


		$newsign = base64_decode($match_result[1][0]);
		
		File::put($this->signature_url, $newsign);
		
		echo 'signing the manifest<br>';
	}
	
	private function compress_pass_file()
	{
		$it = new \RecursiveDirectoryIterator($this->temporary_path);
		
		$zip = new \ZipArchive();
		
		$res = $zip->open($this->output_url, \ZipArchive::CREATE);
		
		if($res === true)
		{
		
			foreach(new \RecursiveIteratorIterator($it) as $file)
			{
				echo $file->getPathname().'<br>';
				$zip->addFile($file->getPathname(), $file->getBasename());
			}
			$zip->close();
			echo 'zipped';
		}
		else
		{
			echo 'unable to create zip file';
		}
	}
	
	private function delete_temporary_directory()
	{
		File::rmdir($this->temporary_path);
	}
}


?>