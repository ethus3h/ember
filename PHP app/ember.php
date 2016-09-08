<?php

# 0. Header and setup
{
	# 2015apr21

	$emberVersion = "1.0.51";

	#based on the other ember.php, version 8-0.91.44
	error_reporting(E_ALL);
	ini_set("display_errors",1);
	
	$formats = array(
		"dc" => array("Ember Document Format ASCII Dc List","EDF Dc List","*.edc","No","No","This is Ember's native \"pivot\" format."),
		"editabledc" => array("Ember Document Format Editable ASCII Dc List","EDF Dc List","*.edceditable","Yes","No","Coded Dc tags for non-editable characters"),
		"edf_latest" => array("Ember Document Format, latest version (updates). Currently an alias of edf_1_0_44.","Ember Document Format","*.edf","No","No","No notes at this time"),
		"ascii" => array("ASCII text","Legacy text encodings","*.txt","Partial","No","No notes at this time"),
		"asciilatin" => array("ASCII text, Latin letters subset","Legacy text encodings","*.txt","Partial","No","No notes at this time"),
		"html" => array("HTML document","","*.html","No","Partial","No notes at this time"),
		"data" => array("Uninterpreted binary data, in octets","Data","*","No","No","Raw binary data cannot be read or written that is not a multiple of 8 bytes"),
		"edf_1_0_43" => array("Ember Document Format, old, incompatible file format specified in <i>Ember</i> version 1.0.43","EDF 1.0.43 Legacy","*.edf","No","Partial","No notes at this time"),
		"edf_1_0_44" => array("Ember Document Format, current version specified in <i>Ember</i> version 1.0.44","Ember Document Format","*.edf","No","Partial","No notes at this time"),
	);
}

# 1. Set up utilitarian functions that I need.
{
	#Utilities
	{
		function log_add($text) {
			echo $text;
		}
		function rq($name,$returnEmptyIfUndefined = false) {
			# Return a request variable
			if(!isset($_REQUEST[$name])) {
				if($returnEmptyIfUndefined) {
					return '';
				}
				return new Exception('Unset variable');
			}
			else {
				return $_REQUEST[$name];
			}
		}

		#Baggage Claim class from Fracture//Active
		{
			//baggage_claim is a hacky utility class for transferring data from one part of a script to another without worrying about variable scope
			class baggage_claim
			{
				public $temp_temp_table;
				public $tableid;
				public $table;
				public $next;
				function check_luggage($variable, $new_content)
				{
					$this->$variable = $new_content;
				}
				function claim_luggage($variable)
				{
					return $this->$variable;
				}
			}
			global $baggage_claim;
			$baggage_claim = new baggage_claim;
		}
		
		#String functions
		{
		
			#String functions based on Weave: str_trunc, strrposlimit, shorten
			{
				function str_trunc($str, $max, $strict = TRUE, $trunc = '')
				{
					// Returns a trunctated version of $str up to $max chars, excluding $trunc.
					//Not written by me.
					// $strict = FALSE will allow longer strings to fit the last word.
					if (strlen($str) <= $max) {
						return $str;
					} else {
						if ($strict) {
							return substr($str, 0, strrposlimit($str, ' ', 0, $max + 1)) . $trunc;
						} else {
							$strloc = strpos($str, ' ', $max);
							if (strlen($strloc) != 0) {
								return substr($str, 0, $strloc) . $trunc;
							} else {
								return $str;
							}
						}
					}
				}
				function strrposlimit($haystack, $needle, $offset = 0, $limit = NULL)
				{
					// Works like strrpos, but allows a limit
					//Not written by me.
					if ($limit === NULL) {
						return strrpos($haystack, $needle, $offset);
					} else {
						$search = substr($haystack, $offset, $limit);
						return strrpos($search, $needle, 0);
					}
				}
				function shorten($content)
				{
					//Shorten a string
					if (strlen($content) > 32) {
						//trim to 64 but round by words
						$shortenedstring = str_trunc($content, 32) . "…";
						global $baggage_claim;
						$baggage_claim->check_luggage('Shortened', 'true');
						if (strlen($shortenedstring) > 40) {
							//trim to 64
							$shortenedstring = substr($content, 0, 32) . "…";
						}
					} else {
						$shortenedstring = $content;
						global $baggage_claim;
						$baggage_claim->check_luggage('Shortened', 'false');
					}
					if(strlen($shortenedstring)>38) {
						return $shortenedstring;
					}
					else {
						$shortenedstring = substr($content,0,64);
						if(strlen($shortenedstring)<strlen($content)) {
							$shortenedstring = $shortenedstring.'…';
						}
						return $shortenedstring;
					}
				}
			}
		
			function formatFilename($name,$format) {
				global $formats;
				if(array_key_exists($format,$formats)) {
					$formatData = $formats[$format];
					return str_replace("*",$name,$formatData[2]);
				}
				return new Exception('Unknown format');
			}
		
			function html_sanitize($data) {
				return htmlspecialchars($data);
			}
		}
		
		#Database functions
		{
			class Db {
				#partly based on FractureDB
				function query($query)
				{
					#Make sure $this->databaseName is set
					$this->databaseName = $this->databaseName;
					$dbh = $this->db;
					$this->queryCount++;
					$result = $dbh->prepare($query);
					$result->execute();
					if (stripos($query, 'INSERT') === 0) {
						return 'Inserted';
					}
					if (stripos($query, 'UPDATE') === 0) {
						return 'Updated';
					}
					return $result->fetchAll(PDO::FETCH_ASSOC);
				}
				
				function getRows($tableName,$filters) {
					$query = 'SELECT * FROM '.$tableName.' WHERE '.$filters.';';
					return $this->query($query);
				}
				
				function getTable($tableName) {
					$query = 'SELECT * FROM ' . $tableName . ' ORDER BY id;';
					return $this->query($query);
				}
				
				function displayTable($tableName,$regionIdentifier,$highlightedFieldValues = array()) {
					$data = $this->getTable($tableName);
					echo '<a href="ember.php?action='.rq('action').'&table='.rq('table').'&editTable=true">Edit table</a>';
					foreach($data as $index=>$row) {
						echo "<tr>";
						foreach($row as $columnName=>$value) {
							echo '<td class="'.$regionIdentifier.'_'.$columnName.'">'.$value."</td>";
						}
						echo "</tr>";
					}
				}

				function editTable($tableName,$regionIdentifier) {
					#partly based on discosync
					$data = $this->getTable($tableName);
					echo getSyncFunction($this->databaseName,$tableName);
					echo '<a href="ember.php?action='.rq('action').'&table='.rq('table').'">Done editing</a> | <a href="ember.php?action=addRowsToTableAPI&db='.$this->databaseName.'&copyRows=true&numberOfRows=5&table='.rq('table').'">Add 5 rows (will be copied from last row)</a> | <a href="ember.php?action=addRowsToTableAPI&db='.$this->databaseName.'&copyRows=false&numberOfRows=5&table='.rq('table').'">Add 5 blank rows</a>';
					foreach($data as $index=>$row) {
						echo "<tr>";
						$i = 0;
						foreach($row as $columnName=>$value) {
							echo '<td class="'.$regionIdentifier.'_'.$columnName.'"><input type="text" id="'.$regionIdentifier.'_row'.$row['id'].'_'.$columnName.'" onkeypress="syncDataField.call(this,event,\''.$regionIdentifier.'\',\''.$row['id'].'\',\''.$columnName.'\',\''.($i).'\');" value="'.html_sanitize($value).'"></td>';
							$nextSiblings = str_repeat('.nextSibling',$i*2);
							#echo getSyncFunction($this->databaseName,$tableName,$row['id'],$columnName,$regionIdentifier.'_row'.$row['id'].'_'.$columnName,$nextSiblings);
							$i++;
						}
						echo "</tr>";
					}
				}
				
				function updateDatabaseField($table,$row,$column,$value) {
					#help from http://www.w3schools.com/sql/sql_update.asp
					echo $this->query('UPDATE '.$table.' SET '.$column.'='.$this->db->quote($value).' WHERE id=\''.$row.'\';');
				}
				
				function addRowsToTable($table,$copyRows,$numberOfRows) {
					$data = $this->getTable($table);
					$rowToCopy = end($data);
					$nextRowID = $rowToCopy['id']+1;
					$i = 0;
					while($i < $numberOfRows) {
						if($copyRows == "true") {
							$this->query("INSERT INTO ".$table." (id) VALUES ('".$nextRowID."');");
							foreach($rowToCopy as $column=>$value) {
								if($column !== "id") {
									$this->query('UPDATE '.$table.' SET '.$column.'='.$this->db->quote($value).' WHERE id=\''.$nextRowID.'\';');
								}
							}
						}
						if($copyRows == "false") {
							#help from http://stackoverflow.com/questions/13605208/how-to-insert-an-empty-line-to-sql-table
							#$this->query("INSERT INTO ".$table." DEFAULT VALUES;");
							$this->query("INSERT INTO ".$table." (id) VALUES ('".$nextRowID."');");
						}
						$nextRowID++;
						$i++;
					}				
					echo 'Done!';	
				}
				
				function getLastID($table) {
					#help from http://stackoverflow.com/questions/16436485/sqlite-selecting-a-value-which-has-maximum-id-number
					return $this->query('SELECT * FROM '.$table.' ORDER BY id DESC LIMIT 1;');
				}
				
				function close() {
					$this->db = null;
				}
			}
			class SqliteDb extends Db {
				#help from http://www.if-not-true-then-false.com/2012/php-pdo-sqlite3-example/?PageSpeed=noscript
				function __construct($name) {
					$this->db = new PDO('sqlite:'.$name);
					$this->databaseName = $name;
					$this->queryCount = 0;
					#Help from Sammitch in IRC 2015mar20
					$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				}
			}
			
			function getDbObjectByName($databaseName) {
				switch($databaseName) {
					case 'archives.sqlite':
						return new SqliteDb($databaseName);
						break;
					case 'edf.sqlite':
						return new SqliteDb($databaseName);
						break;
					case 'dceditor.sqlite':
						return new SqliteDb($databaseName);
						break;
				}
			}
		}
	}
	#Values
	{
		function getPaddedTimezone() {
		$timezone = date('Z');
		if($timezone[0]=='-') {
			$timezone = str_pad($timezone,6);
		}
		else {
			$timezone = str_pad('+'.$timezone,6);
		}
		return $timezone;
		}
	}
	#Hash functions
	{
		function md5s($data) { 
			return md5($data);
		}
	
		function sha1s($data) {
			return sha1($data);
		}
	
		function sha512s($data) {
			return hash('sha512',$data);
		}
	}
	#Data converters
	{
		class Document {
			/*
			Hello_World!:

<?xml version="1.0" encoding="ASCII"?>
<dcStructure id="XX">
	<dc57/><dc86/><dc93/><dc93/><dc96/><dc80/><dc72/><dc96/><dc99/><dc93/><dc85/><dc19/>
</dcStructure id="YY">

			*/
		}
		function byteToDc($sourceFormat,$byte) {
			$byte = strtoupper($byte);
			$db = new SqliteDb("edf.sqlite");
			if($sourceFormat == 'utf32') {
				$sourceFormat = 'unicode';
				$byte = ltrim($byte,'0');
			}
			$data = $db->getRows("encodings",'source_encoding = "'.$sourceFormat.'" AND source_bytes = "'.$byte.'"');
			if(array_key_exists('0',$data)) {
				$data = $data[0];
				$data = $data['target_dc'];
			}
			else {
				#$data = '';
				#throw new Exception('No mapping found');
				$data = '207';
			}
			return $data;
		}
		function DcToByte($dc) {
			#echo 'Dc to byte: '.$dc.'...';
			$db = new SqliteDb("edf.sqlite");
			$data = $db->getRows("dcs",'id = "'.$dc.'"');
			if(array_key_exists('0',$data)) {
				$data = $data[0];
				$data = $data['unicode'];
			}
			else {
				throw new Exception('No mapping found');
			}
			if(strlen($data) == 0) {
				$data = 'FFFD';
			}
			if(!is_integer(strlen($data)/2)) {
				$data = '0' . $data;
			}
			return $data;
		}
		
		function DcToHTML($dc) {
			$db = new SqliteDb("edf.sqlite");
			$data = $db->getRows("dcs",'id = "'.$dc.'"');
			if(array_key_exists('0',$data)) {
				$data = $data[0];
				$data = $data['htmlEquiv'];
			}
			else {
				throw new Exception('No mapping found');
			}
			return $data;
		}
		
		function convert($data,$sourceFormat,$targetFormat,$options=array()) {
			#return "Source: ".$sourceFormat."\n\n Target: ".$targetFormat;
			$dc = '';
			$dc = $dc . '235';
			if($sourceFormat == 'ascii') {
				$dataWorkingCopy = strtoupper(bin2hex($data));
				while(strlen($dataWorkingCopy)>0) {
					$byte = substr($dataWorkingCopy,0,2);
					if(hexdec($byte) > 127) {
						throw new Exception('Data is not in ASCII format');
					}
					$converted = byteToDc($sourceFormat,$byte);
					#echo $converted;
					$dc = $dc . ',' . $converted;
					#print_r($element);
					$dataWorkingCopy = substr($dataWorkingCopy,2);
				}
			}
			
			if($sourceFormat == 'utf8') {
				$data = hex2bin(substr(bin2hex(iconv('UTF-8','UTF-32',$data)),8));
				#echo bin2hex($data);
				$dc = $dc . substr(convert($data,'utf32','dc'),3,-4);
				#$dc = $dc . $data;
			}
			
			if($sourceFormat == 'utf32') {
				$dataWorkingCopy = strtoupper(bin2hex($data));
				#echo '<br>DWC = '.$dataWorkingCopy.'</br>';
				#$dc = $dc . $dataWorkingCopy;
				while(strlen($dataWorkingCopy)>0) {
					$byte = substr($dataWorkingCopy,0,8);
					$converted = byteToDc($sourceFormat,$byte);
					$dc = $dc . ',' . $converted;
					$dataWorkingCopy = substr($dataWorkingCopy,8);
				}
			}

			if($sourceFormat == 'editabledc') { 
			
				{
			
				/*		Possibilities: @, @@, @a, @a@, @1@, a@a, a@1@, a@a@, a@@, a@@a, a@@@a			
					while strlen data > 0:
						if(first character is @):
							if another @ exists:
								cut from 1st to 2nd @
							If no other @ exists:
								remove @
						if first character is not @:
							if an @ exists:
								cut to 1st @
							if no @ exists:
								decode whole string
						break;*/
				}
				$originalData = $data;	
				while (strlen($data) > 0) {
					log_add('<br><b>Beginning parse iteration.</b><br><br>');
					if(substr($data,0,1) == '@') {
						log_add('<br>String begins with @-code.<br><br>');
						$dataWithoutFirstAt = substr($data,1);
						if(strpos($dataWithoutFirstAt,'@') !== false) {
							log_add('<br>@-code has closing @ sign; trimming and appending to $dc<br><br>');
							//cut from 1st to 2nd @
							$firstAtCode = substr($data,0,strpos($dataWithoutFirstAt,'@')+2);
							log_add('<br>@-code to process: '.$firstAtCode.'<br><br>');
							$data = substr($data,strpos($dataWithoutFirstAt,'@')+2);
							log_add('<br>Remaining string to process: '.$data.'.<br><br>');
							
							
							//decode @-code
							{							
							

							#TODO
							
							$atCode = substr($firstAtCode,1,-1);
							
							if(strlen($atCode) === 0) {
								log_add('<br>No contents of @-code, append nothing.<br><br>');
							}
							else {
								if(ctype_digit((string)$atCode)) {
									log_add('<br>This is a valid @-code! Append to $dc.<br><br>');
									$dc = $dc . ',' . $atCode;
								}
							
								else {
									log_add('<br>Not a valid @-code, because it\'s not a positive or zero integer inside.<br><br>');
								}
							}
							
							
							}
							log_add('<br>$dc = '. $dc . '<br><br>');
						}
						else {
							log_add('<br>No closing @ sign; removing initial @ and continuing.<br><br>');
							$data = substr($data,1);
						}
					}
					//if first character is not @:
					else {
						log_add('<br>String does not begin with an @ code.<br><br>');
						//if an @ exists:
						if(strpos($data,'@') !== false) {
							log_add('<br>String contains an @ sign, so beginning will be trimmed, converted, and appended.<br><br>');
							//cut to 1st @
							$stringToConvertAndAppend = substr($data,0,strpos($data,'@'));
							log_add('<br>Beginning string to process: '.$stringToConvertAndAppend.'.<br><br>');
							$data = substr($data,strpos($data,'@'));
							log_add('<br>Remaining string to process: '.$data.'.<br><br>');
							//decode string and append to $dc
							$dc = $dc . substr(convert($stringToConvertAndAppend,'utf8','dc'),3,-4);
						}
						//if no @ exists:
						else {
							log_add('<br>String does not contain an @ sign; entire string will be converted and appended.<br><br>');
							//decode whole string
							$dc = $dc . substr(convert($data,'utf8','dc'),3,-4);
							$data = '';
						}
					}
					log_add('<br>Dc and end of iteration: '.$dc.'.<br><br>');
				}
				
				{
// 				while(strlen($data) > 0) {
// 					if(strpos($data,'@') === 0) {
// 						$data = substr($data,1);
// 						if(strlen(substr($data,0,strpos($data,'@'))) == 0) {
// 							$dc = $dc . ',1';
// 						}
// 						else {
// 							$dc = $dc . ',' . substr($data,0,strpos($data,'@'));
// 						}
// 						$data = substr($data,strpos($data,'@')+1);
// 					}
// 					else {
// 						if(strpos($data,'@') !== false) {
// 							$dc = $dc . substr(convert(substr($data,0,strpos($data,'@')),'utf8','dc'),3,-4);
// 							$data = '';
// 						}
// 						else {
// 							$dc = $dc . substr(convert($data,'utf8','dc'),3,-4);
// 							$data = '';
// 						}
// 						#$dc = $dc . substr($data,0);
// 						#$dc = $dc . substr(convert(substr($data,0,strpos($data,'@')),'utf8','dc'),3,-4);
// 						#$data = substr($data,strpos($data,'@')+1);
// 					}
// 				}
				}
				
				log_add('<br><br>Done processing. Final $dc: ' . $dc . '<br><br>');
				$dc = str_replace('@','',$dc);
			}



			$dc = $dc . ',236';
			//remove empty dcs (",," in $dc)
			//based on http://stackoverflow.com/questions/6723389/remove-repeating-character
			$dc = preg_replace("/(,)\\1+/","$1",$dc);
			



			if($targetFormat == 'html') {
				#go through $dc, and change each dc to html
				$output = '';
				#echo $dc;
				while(strlen($dc) > 0) {
					if(strpos($dc,',') !== false) {
						$singledc = substr($dc,0,strpos($dc,','));
						$converted = DcToHTML(str_replace(',','',$singledc));
						$output = $output . $converted;
						$dc = substr($dc,strpos($dc,',')+1);
					}
					else {
						$converted = DcToHTML(str_replace(',','',$dc));
						$output = $output . $converted;
						$dc = '';
					}
				}
				return $output;				
			}

			if($targetFormat == 'editabledc') {
				return $originalData;
			}
			
			if($targetFormat == 'dc') {
				return $dc;
			}
			
			if($targetFormat == 'ascii') {
				$output = '';
				while(strlen($dc) > 0) {
					#echo 'Dc remaining to convert: '.$dc.'Doom!';
					#help from / based on http://stackoverflow.com/questions/4366730/check-if-string-contains-specific-words
					if(strpos($dc,',') !== false) {
						$singledc = substr($dc,0,strpos($dc,','));
						$converted = DcToByte(str_replace(',','',$singledc));
						if(hexdec($converted) > 127) {
							$converted = '';
						}
						$output = $output . $converted;
						#echo 'Output:'.$converted;
						$dc = substr($dc,strpos($dc,',')+1);
					}
					else {
						#echo 'DONE DONE DONE DONE';
						$converted = DcToByte(str_replace(',','',$dc));
						if(hexdec($converted) > 127) {
							$converted = '';
						}
						$output = $output . $converted;
						$dc = '';
					}
				}
				return hex2bin($output);
			}
			
			/* if($sourceFormat == $targetFormat) {
				return $data;
			}
			if($sourceFormat == 'ascii' && $targetFormat == 'edf_1_0_43') {
				if(array_key_exists('version',$options)) {
					$version = $options['version'];
				}
				else {
					$version = '';
				}
				return convert_ascii_to_edf_1_0_43($data,$version);
			}
			
			if($sourceFormat == 'ascii' && $targetFormat == 'edf_1_0_44') {
				if(array_key_exists('comments',$options)) {
					$version = $options['comments'];
				}
				else {
					$comments = '';
				}
				return convert_ascii_to_edf_1_0_44($data,$comments);
			}
			
			if($sourceFormat == 'asciilatin' && $targetFormat == 'dc') {
				return convert_asciilatin_to_dc($data);
			}
			*/
			
			return new Exception('Unknown data conversion pair');
		}
		
		function getConvertedDataFromRequest($base64request = true) {
			$input = rq('dataEntered');
			if($base64request) {
				$input = base64_decode($input);
			}
			
			if(rq("hexInput") == "1") {
				$input = hex2bin($input);
			}
			$output = convert($input,rq('inputFormat'),rq('outputFormat'));
			if(rq("hexOutput") == "1") {
				$output = bin2hex($output);
			}
			if(rq("action") == "getConvertedDataAPI") {
				//$output = htmlspecialchars($output);
				//Don't remember why I did the htmlspecialchars thing here.... but it's getting in the way now
			}
			return $output;
		}
		
		#Parsers: X to Dc converters
		{
			function convert_asciilatin_to_dc($data) {
			}
		}

		#Writers: Dc to X converters
		{
		}

		#Standalone converters
		{
			function convert_ascii_to_edf_1_0_43($content,$version='') {
				return convert_data_to_edf_1_0_43_wrapped_raw($content,$version);
			}
			function convert_ascii_to_edf_1_0_44($content,$comments = '') {
				return convert_data_to_edf_wrapped_raw($content,$comments);
			}
			function convert_data_to_edf_1_0_43_wrapped_raw($content,$version = '') {
				#Return an EDF document body $content as an EDF document.
				if(strlen($version)==0){
					$version = "1".str_repeat(" ",971);
				}
				#date from http://php.net/manual/en/function.date.php
				global $emberVersion;
				$authorIdentifier = str_pad("PUAI:ember.php ".$emberVersion."-generated ".date("F j, Y, g:i a"),566);
				$header = hex2bin("89")."EDFe".hex2bin("0D0A1A0AFEFF")."|http://futuramerlin.com/|Format version:";
				$header = $header.$version;
				$header = $header."|MD5:".md5s($content)."|SHA1:".sha1s($content)."|SHA512:".sha512s($content)."|Author Identifier:";
				$header = $header.$authorIdentifier;
				$header = $header."|MD5:".md5s($header)."|SHA1:".sha1s($header)."|SHA512:".sha512s($header);
				$header = $header.hex2bin("00");
				return $header.$content;
			}
			function convert_data_to_edf_wrapped_raw($content,$comments = '') {
				#Return an EDF document body $content as an EDF document.
				$version = "1_0_44".str_repeat(" ",966);
				$location = ' Ember PHP script';
				#date from http://php.net/manual/en/function.date.php
				global $emberVersion;
				$authorIdentifier = str_pad("PUAI:ember.php ".$emberVersion."-generated ".date("F j, Y, g:i a"),567);
				$creationMetadata = str_pad("",20)."|Creation time:".date('+00Y-m-d H.i.s.u ').getPaddedTimezone()." ".str_pad(" (PHP date() result, with two extra 00s and extra + at the beginning of the year)",32);
				$creationMetadata = str_pad($creationMetadata."|Creation location: ".$location,1517);
				$comments = str_pad($comments,501);
				$header = hex2bin("89")."EDFe".hex2bin("0D0A1AFEFF0A")."|http://futuramerlin.com/|Format version:";
				$header = $header.$version;
				$header = $header."|MD5:".md5s($content)."|SHA1:".sha1s($content)."|SHA512:".sha512s($content)."|Author Identifier:";
				$header = $header.$authorIdentifier;
				$header = $header."|Creation metadata:".$creationMetadata;
				$header = $header."|Comments:".$comments;
				$header = $header."|MD5:".md5s($header)."|SHA1:".sha1s($header)."|SHA512:".sha512s($header);
				$header = $header.hex2bin("00");
				return $header.$content;
			}
		}
	}
}

# 2. Set up procedures I'll use.
{
	#Code snippets
	{
		function createHtmlPage($title="Ember",$head="",$doctype="<!DOCTYPE html>") {
			#jquery code from http://www.w3schools.com/jquery/jquery_get_started.asp
			#Help from http://stackoverflow.com/questions/24813094/how-to-call-a-jquery-function-in-html-body-onload
			echo $doctype.'<html><head><script src="jquery-1.11.2.min.js"></script><script src="jquery.floatThead.min.js"></script><script>				
			 $(document).ready(function() { 
			 $("table:first").floatThead();});</script><title>'.$title.'</title>'.$head.'</head><body>';
		}
		function endHtmlPage() {
			echo '</body></html>';
		}
		function getHelloWorld($format = 'ascii') {
			if($format == '') { $format = 'ascii'; }
			return convert("Hello World!",'ascii',$format);
		}
		function getTableStyle() {
			return '<style>table, th {border:1px solid; background-color:#ffffff;}input { width:100%; } tr, td {border:1px dotted;} .highlightedCell, .dcreference_name { background-color:#FFFFCC; }.floatingHeader {  position: fixed;  top: 0;  visibility: hidden;}</style>';
		}
		function getSyncFunction($database,$table) {
			#partly based on discosync
			#http://stackoverflow.com/questions/12407093/focus-the-next-input-with-down-arrow-key-as-with-the-tab-key			
			# onkeypress="syncDataField.call(this,event,'.$regionIdentifier.','.$row['id'].','.$columnName.'$i*2)
			{
				//from file:///Volumes/disk2s1/Archive/Reference/Sciences/Computer%20Science/Programming/Mozilla%20Developer%20Network%20%28tweaked,%20latest%20version%20as%20of%202015-03-12%29/en-US/docs/Web/JavaScript/Base64_encoding_and_decoding.html#Solution_.232_.E2.80.93_rewriting_atob%28%29_and_btoa%28%29_using_TypedArrays_and_UTF-8
				/* examples: */
				{
				/* 
				var sMyInput = "Base 64 \u2014 Mozilla Developer Network";
				var aMyUTF8Input = strToUTF8Arr(sMyInput);
				var sMyBase64 = base64EncArr(aMyUTF8Input);
				alert(sMyBase64);
				var aMyUTF8Output = base64DecToArr(sMyBase64);
				var sMyOutput = UTF8ArrToStr(aMyUTF8Output);
				alert(sMyOutput);
				*/
				}
				{
				$utf8utils = '"use strict";

					/*\
					|*|
					|*|  Base64 / binary data / UTF-8 strings utilities
					|*|
					|*|  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Base64_encoding_and_decoding
					|*|
					\*/

					/* Array of bytes to base64 string decoding */

					function b64ToUint6 (nChr) {

					  return nChr > 64 && nChr < 91 ?
						  nChr - 65
						: nChr > 96 && nChr < 123 ?
						  nChr - 71
						: nChr > 47 && nChr < 58 ?
						  nChr + 4
						: nChr === 43 ?
						  62
						: nChr === 47 ?
						  63
						:
						  0;

					}

					function base64DecToArr (sBase64, nBlocksSize) {

					  var
						sB64Enc = sBase64.replace(/[^A-Za-z0-9\+\/]/g, ""), nInLen = sB64Enc.length,
						nOutLen = nBlocksSize ? Math.ceil((nInLen * 3 + 1 >> 2) / nBlocksSize) * nBlocksSize : nInLen * 3 + 1 >> 2, taBytes = new Uint8Array(nOutLen);

					  for (var nMod3, nMod4, nUint24 = 0, nOutIdx = 0, nInIdx = 0; nInIdx < nInLen; nInIdx++) {
						nMod4 = nInIdx & 3;
						nUint24 |= b64ToUint6(sB64Enc.charCodeAt(nInIdx)) << 18 - 6 * nMod4;
						if (nMod4 === 3 || nInLen - nInIdx === 1) {
						  for (nMod3 = 0; nMod3 < 3 && nOutIdx < nOutLen; nMod3++, nOutIdx++) {
							taBytes[nOutIdx] = nUint24 >>> (16 >>> nMod3 & 24) & 255;
						  }
						  nUint24 = 0;

						}
					  }

					  return taBytes;
					}

					/* Base64 string to array encoding */

					function uint6ToB64 (nUint6) {

					  return nUint6 < 26 ?
						  nUint6 + 65
						: nUint6 < 52 ?
						  nUint6 + 71
						: nUint6 < 62 ?
						  nUint6 - 4
						: nUint6 === 62 ?
						  43
						: nUint6 === 63 ?
						  47
						:
						  65;

					}

					function base64EncArr (aBytes) {

					  var nMod3 = 2, sB64Enc = "";

					  for (var nLen = aBytes.length, nUint24 = 0, nIdx = 0; nIdx < nLen; nIdx++) {
						nMod3 = nIdx % 3;
						if (nIdx > 0 && (nIdx * 4 / 3) % 76 === 0) { sB64Enc += "\r\n"; }
						nUint24 |= aBytes[nIdx] << (16 >>> nMod3 & 24);
						if (nMod3 === 2 || aBytes.length - nIdx === 1) {
						  sB64Enc += String.fromCharCode(uint6ToB64(nUint24 >>> 18 & 63), uint6ToB64(nUint24 >>> 12 & 63), uint6ToB64(nUint24 >>> 6 & 63), uint6ToB64(nUint24 & 63));
						  nUint24 = 0;
						}
					  }

					  return sB64Enc.substr(0, sB64Enc.length - 2 + nMod3) + (nMod3 === 2 ? '' : nMod3 === 1 ? '=' : '==');

					}

					/* UTF-8 array to DOMString and vice versa */

					function UTF8ArrToStr (aBytes) {

					  var sView = "";

					  for (var nPart, nLen = aBytes.length, nIdx = 0; nIdx < nLen; nIdx++) {
						nPart = aBytes[nIdx];
						sView += String.fromCharCode(
						  nPart > 251 && nPart < 254 && nIdx + 5 < nLen ? /* six bytes */
							/* (nPart - 252 << 30) may be not so safe in ECMAScript! So...: */
							(nPart - 252) * 1073741824 + (aBytes[++nIdx] - 128 << 24) + (aBytes[++nIdx] - 128 << 18) + (aBytes[++nIdx] - 128 << 12) + (aBytes[++nIdx] - 128 << 6) + aBytes[++nIdx] - 128
						  : nPart > 247 && nPart < 252 && nIdx + 4 < nLen ? /* five bytes */
							(nPart - 248 << 24) + (aBytes[++nIdx] - 128 << 18) + (aBytes[++nIdx] - 128 << 12) + (aBytes[++nIdx] - 128 << 6) + aBytes[++nIdx] - 128
						  : nPart > 239 && nPart < 248 && nIdx + 3 < nLen ? /* four bytes */
							(nPart - 240 << 18) + (aBytes[++nIdx] - 128 << 12) + (aBytes[++nIdx] - 128 << 6) + aBytes[++nIdx] - 128
						  : nPart > 223 && nPart < 240 && nIdx + 2 < nLen ? /* three bytes */
							(nPart - 224 << 12) + (aBytes[++nIdx] - 128 << 6) + aBytes[++nIdx] - 128
						  : nPart > 191 && nPart < 224 && nIdx + 1 < nLen ? /* two bytes */
							(nPart - 192 << 6) + aBytes[++nIdx] - 128
						  : /* nPart < 127 ? */ /* one byte */
							nPart
						);
					  }

					  return sView;

					}

					function strToUTF8Arr (sDOMStr) {

					  var aBytes, nChr, nStrLen = sDOMStr.length, nArrLen = 0;

					  /* mapping... */

					  for (var nMapIdx = 0; nMapIdx < nStrLen; nMapIdx++) {
						nChr = sDOMStr.charCodeAt(nMapIdx);
						nArrLen += nChr < 0x80 ? 1 : nChr < 0x800 ? 2 : nChr < 0x10000 ? 3 : nChr < 0x200000 ? 4 : nChr < 0x4000000 ? 5 : 6;
					  }

					  aBytes = new Uint8Array(nArrLen);

					  /* transcription... */

					  for (var nIdx = 0, nChrIdx = 0; nIdx < nArrLen; nChrIdx++) {
						nChr = sDOMStr.charCodeAt(nChrIdx);
						if (nChr < 128) {
						  /* one byte */
						  aBytes[nIdx++] = nChr;
						} else if (nChr < 0x800) {
						  /* two bytes */
						  aBytes[nIdx++] = 192 + (nChr >>> 6);
						  aBytes[nIdx++] = 128 + (nChr & 63);
						} else if (nChr < 0x10000) {
						  /* three bytes */
						  aBytes[nIdx++] = 224 + (nChr >>> 12);
						  aBytes[nIdx++] = 128 + (nChr >>> 6 & 63);
						  aBytes[nIdx++] = 128 + (nChr & 63);
						} else if (nChr < 0x200000) {
						  /* four bytes */
						  aBytes[nIdx++] = 240 + (nChr >>> 18);
						  aBytes[nIdx++] = 128 + (nChr >>> 12 & 63);
						  aBytes[nIdx++] = 128 + (nChr >>> 6 & 63);
						  aBytes[nIdx++] = 128 + (nChr & 63);
						} else if (nChr < 0x4000000) {
						  /* five bytes */
						  aBytes[nIdx++] = 248 + (nChr >>> 24);
						  aBytes[nIdx++] = 128 + (nChr >>> 18 & 63);
						  aBytes[nIdx++] = 128 + (nChr >>> 12 & 63);
						  aBytes[nIdx++] = 128 + (nChr >>> 6 & 63);
						  aBytes[nIdx++] = 128 + (nChr & 63);
						} else /* if (nChr <= 0x7fffffff) */ {
						  /* six bytes */
						  aBytes[nIdx++] = 252 + (nChr >>> 30);
						  aBytes[nIdx++] = 128 + (nChr >>> 24 & 63);
						  aBytes[nIdx++] = 128 + (nChr >>> 18 & 63);
						  aBytes[nIdx++] = 128 + (nChr >>> 12 & 63);
						  aBytes[nIdx++] = 128 + (nChr >>> 6 & 63);
						  aBytes[nIdx++] = 128 + (nChr & 63);
						}
					  }

					  return aBytes;

					}';
				}
			}
			
			return '<script type="text/javascript"> ' . $utf8utils . '
				function syncDataField(e,regionIdentifier,rowId,columnName,nextSiblingCount,action,outputId) {
					//help from http://stackoverflow.com/questions/12797118/how-can-i-declare-optional-function-parameters-in-javascript
					action = action || "syncTableField";
					outputId = outputId || "";
					if (e.keyCode==40) {
						var node = this.parentNode.parentNode.nextSibling.firstChild;
						var i = 0;
						while (i<nextSiblingCount) {
							node = node.nextSibling;
							i++;
						}
						node = node.firstChild;
						//         inpu td         tr         next tr     first td    next td         input
						node.focus();
						node.select();
					}
					if (e.keyCode==38) {
						var node = this.parentNode.parentNode.previousSibling.firstChild;
						var i = 0;
						while (i<nextSiblingCount) {
							node = node.nextSibling;
							i++;
						}
						node = node.firstChild;
						//         inpu td         tr         prev tr         first td    next td         input
						node.focus();
						node.select();
					}
					//help from http://www.toptal.com/javascript/10-most-common-javascript-mistakes
					var self = this;
					setTimeout(
						function () {
							var elementToSync = self.innerHTML;
							var elementToSync = self.value;
							
							var xmlhttp;
							if (window.XMLHttpRequest) { 
								xmlhttp=new XMLHttpRequest();
							} else {
								xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
							} 
							//help from http://stackoverflow.com/questions/18251399/why-doesnt-encodeuricomponent-encode-sinlge-quotes-apostrophes
							if(action=="syncTableField") {
								var send="action=updateDatabaseFieldAPI&db='.$database.'&dataTargetTable='.$table.'&dataTargetRow="+rowId+"&dataTargetColumn="+columnName+"&dataValue="+encodeURIComponent(btoa(elementToSync)).replace(/[!\'()*]/g, escape);
							}
							if(action=="updateConverter") {
								var send="action=getConvertedDataAPI"+
								"&inputFormat="+document.getElementById("inputFormat").value+
								"&hexInput="+document.getElementById("hexInput").value+
								"&outputFormat="+document.getElementById("outputFormat").value+
								"&hexOutput="+document.getElementById("hexOutput").value+
								"&dataEntered="+encodeURIComponent(
									btoa(
										document.getElementById(
											"dataEntered"
										)
									.value
									)
								)
								.replace(/[!\'()*]/g, escape)
								;
							}
							
							//based on dceutils
							xmlhttp.onreadystatechange=function()
							{
								if (xmlhttp.readyState==4 && xmlhttp.status==200)
								{
									if(document.getElementById(outputId)) {
										//help from / based on http://stackoverflow.com/questions/5007574/rendering-plaintext-as-html-maintaining-whitespace-without-pre
										document.getElementById(outputId).innerHTML=xmlhttp.responseText.replace(/\t/g, "    ")
										   .replace(/  /g, "&nbsp; ")
										   .replace(/  /g, " &nbsp;") // second pass
																	  // handles odd number of spaces, where we 
																	  // end up with "&nbsp;" + " " + " "
										   .replace(/\r\n|\n|\r/g, "<br />");
									}
								}
							}
  							xmlhttp.open("POST","ember.php",true); xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					
							xmlhttp.send(send); 
					
						},
					100); 
            	} 
				</script>';
		}
	}
	#Tests
	{
		function runTests() {
			$t = new Tester();
			$t->test("ASCII to ASCII",convert("Hello World!",'ascii','ascii'),"Hello World!");
			$t->test("ASCII to EDF 1.0.43 and back",convert(convert("Hello World!",'ascii','edf_1_0_43'),'edf_1_0_43','ascii'),"Hello World!");
			$t->testLcMatches("ASCII to EDF 1.0.43",bin2hex(convert("Hello World!",'ascii','edf_1_0_43')),"89454446650D0A1A0AFEFF7C687474703A2F2F6675747572616D65726C696E2E636F6D2F7C466F726D61742076657273696F6E3A31.+");
			return $t->results();
		}
		class Tester
		{
			#based on dceutils 2.6
			protected $results = '';
			public function test($name, $result, $desiredValue)
			{
				if ($result == $desiredValue) {
					$test_passfail = '<font color="green">PASS</font>';
				} else {
					$test_passfail = '<font color="red">FAIL</font>';
				}
				$this->results = $this->results.'<b>'.$name.'</b> → ' . $test_passfail . ': <font color="blue">' . shorten($result) . '</font>. Should be: "' . $desiredValue . '". → ' . $test_passfail . '<br>';
			}
			public function testMatches($name, $result, $pattern)
			{
				if (preg_match('/'.$pattern.'/',$result)) {
					$test_passfail = '<font color="green">PASS</font>';
				} else {
					$test_passfail = '<font color="red">FAIL</font>';
				}
				$this->results = $this->results.'<b>'.$name.'</b> → ' . $test_passfail . ': <font color="blue">' . shorten($result) . '</font>. Should match pattern: "' . $pattern . '". → ' . $test_passfail . '<br>';
			}
			public function testLcMatches($name, $result, $pattern)
			{
				if (preg_match('/'.$pattern.'/i',$result)) {
					$test_passfail = '<font color="green">PASS</font>';
				} else {
					$test_passfail = '<font color="red">FAIL</font>';
				}
				$this->results = $this->results.'<b>'.$name.'</b> → ' . $test_passfail . ': <font color="blue">' . shorten($result) . '</font>. Should match pattern: "' . $pattern . '". → ' . $test_passfail . '<br>';
			}
			public function results()
			{
				return $this->results;
			}
		}
	}
	#Main actions
	{
		function showWelcomePage() {
			createHtmlPage();
			echo '<center><h1>Welcome to Ember.</h1><br>
			<ul>
			<li><a href="ember.php?action=showDocumentation">Documentation</a></li>
			<li><a href="ember.php?action=dcEditor">dcEditor</a></li>
			<li><a href="ember.php?action=showDiscography">Discography</a></li>
			<li><a href="ember.php?action=showTests">Run and display tests</a></li>
			<li><a href="ember.php?action=showHelloWorld&format=edf_1_0_44">Generate Hello World! demo file</a></li>
			</ul></center>';
			endHtmlPage();
		}
		function conversionUtility() { 
				createHtmlPage("Ember",getTableStyle());
				#help from http://www.w3schools.com/html/html_forms.asp
				echo getSyncFunction('none','none').'<form method="post" action="ember.php"><table style="width:100%;table-layout:fixed;"><tr><th>Edit here<br>
					<small>1 = Hex? </small><input id="hexInput" name="hexInput" style="width:3em;"  onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"/>
					<small>Format? </small>
					<input id="inputFormat" name="inputFormat" style="width:8em;" value="editabledc"  onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"/>
					</th><th>Converted<br><small>1 = Hex? </small>
					<input id="hexOutput" name="hexOutput" style="width:3em;" onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');" /> <small>Format? </small>
					<input id="outputFormat" name="outputFormat" style="width:8em;" value="dc" onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"/></th></tr>
				<tr><td style="width:50%;"><textarea id="dataEntered" name="dataEntered" style="width:100%;height:30em;" onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"></textarea></td><td style="">
				<div style="display:block;height:30em;overflow-x:scroll;" id="outputField"></div></td></tr>
				</table><input type="submit" value="Download"><input type="hidden" name="action" value="downloadConvertedDataAPI"></form>';
				endHtmlPage();
		}
		function dcEditor() {
				#Needs to be able to preload a document by ID from a SQLite database

				createHtmlPage("Ember",getTableStyle());
				#Buttons needed: Save, Download HTML, Download Dc
				
				#If the request says to save the document
					#save the document to the database as a new ID
					#store saved version as variables to use for building the page and saving revision ID in variable
				#else no request options
					#do nothing
				#build page, using variables to fill in default values and to display the revision ID (display "unsaved" if no revision ID)
				
				
				
				#If the request says to save the document
				if(rq('saveDocument') == "1") {
					#save the document to the database as a new ID
					$db = getDbObjectByName("dceditor.sqlite");
					$db->query("INSERT INTO docs (date, contents) VALUES ('now','".rq('documentData')."');");
					#store saved version as variables to use for building the page and saving revision ID in variable
					$savedDocument = rq('documentData');
					$savedDocumentID = $db->getLastID('docs');
				}
				#else no request options
				else {
					#do nothing
				}
				#build page, using variables to fill in default values and to display the revision ID (display "unsaved" if no revision ID)
				echo getSyncFunction('none','none').'<form method="post" action="ember.php" id="dcEditorForm"><table style="width:100%;table-layout:fixed;"><tr><th>Edit here<br>
					<small>1 = Hex? </small><input id="hexInput" name="hexInput" style="width:3em;"  onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"/>
					<small>Format? </small>
					<input id="inputFormat" name="inputFormat" style="width:8em;" value="editabledc"  onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"/>
					</th><th>Converted<br><small>1 = Hex? </small>
					<input id="hexOutput" name="hexOutput" style="width:3em;" onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');" /> <small>Format? </small>
					<input id="outputFormat" name="outputFormat" style="width:8em;" value="html" onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"/></th></tr>
				<tr><td style="width:50%;"><textarea id="dataEntered" name="dataEntered" style="width:100%;height:30em;" onkeypress="syncDataField.call(this,event,\'\',\'\',\'\',\'\',\'updateConverter\',\'outputField\');"></textarea></td><td style="">
				<div style="display:block;height:30em;overflow-x:scroll;
				/*https://css-tricks.com/snippets/css/prevent-long-urls-from-breaking-out-of-container/*/ 
				-ms-word-break: break-all;word-break: break-all;word-wrap: break-word;" id="outputField"></div></td></tr>
				</table><button type="button" onClick="document.getElementById(\'outputFormat\').value=\'editabledc\';document.getElementById(\'dcEditorForm\').submit();" style="width:33% !important";>Download editabledc</button><button type="button" style="width:33% !important";>Save</button><input type="submit" value="Download HTML" style="width:33% !important";><input type="hidden" name="action" value="downloadConvertedDataAPI"></form>';
				endHtmlPage();

		}
		#Documentation
		{
			function showDocumentation() {
				createHtmlPage("Ember",getTableStyle());
				#help from http://jsfiddle.net/dPixie/byB9d/3/
				echo '<h1>Data formats</h1>
				<p>Most significant entries listed at the beginning of the table; other entries sorted by type and then alphabetically</p>
				<table>
				<thead><tr><th>Format</th><th>Class</th><th class="highlightedCell">Format code</th><th>Filename Pattern</th><th>Read</th><th>Write</th><th>Notes</th></tr></thead>';
				global $formats;
				foreach($formats as $format=>$traits) {
					echo "<tr>";
					echo "<td>".$traits[0]."</td>";
					echo "<td>".$traits[1]."</td>";
					echo "<td class=\"highlightedCell\">".$format."</td>";
					echo "<td>".$traits[2]."</td>";
					echo "<td>".$traits[3]."</td>";
					echo "<td>".$traits[4]."</td>";
					echo "<td>".$traits[5]."</td>";
					echo "</tr>";
				}
				echo '</table>';
				echo '<h1>Dc Reference</h1>
				<br>
				<ul>
				<li><a href="ember.php?action=showDceTable&table=dcs">List of Dcs</a></li>
				<li><a href="ember.php?action=conversionUtility">Data conversion utility</a></li>
				<li><a href="ember.php?action=showDceTable&table=encodings">List of encodings, with mappings to and from Dcs</a></li>
				</ul>';
				endHtmlPage();
			}
			function showDceTable() {
				$db = new SqliteDb('edf.sqlite');
				createHtmlPage("Ember",getTableStyle());
				switch(rq('table')) {
					case 'dcs':
						echo '<h1>Dc Reference</h1>';
						echo '<table id="dcreferenceTable">';
						echo '<thead><tr><th>Dc ID</th><th>Glyph</th><th>U+</th><th class="highlightedCell" style="padding-left:50px !important;padding-right:50px !important;">Name</th><th style="padding-left:10px !important;padding-right:10px !important;">Type</th><th style="padding-left:10px !important;padding-right:10px !important;">Script</th><th><small><small>Sort following<br><small>(blank: previous)</small></small></small></th><th>Decomp.</th><th>Depr.</th><th>Description</th><th>Syntax</th><th>Other names</th></tr></thead>';
						if(rq('editTable',true) == 'true') {
							echo $db->editTable("dcs","dcreference");
						}
						else {
							echo $db->displayTable("dcs","dcreference");
						}
						echo '</table>';
						break;
					case 'encodings':
						echo '<h1>Data for known encodings</h1>';
						echo '<table id="dcreferenceTable">';
						echo '<thead><tr><th>ID</th><th>Source Encoding</th><th>Source bytes</th><th>Target Dc</th><th>Comments</th></tr></thead>';
						if(rq('editTable',true) == 'true') {
							echo $db->editTable("encodings","encodings");
						}
						else {
							echo $db->displayTable("encodings","encodings");
						}
						echo '</table>';
						break;
				}
				endHtmlPage();
				$db->close();
			}
			function showArchivalProjectsTable() {
				$db = new SqliteDb('archives.sqlite');
				createHtmlPage("Archival Projects",getTableStyle());
				switch(rq('table')) {
					case 'archives':
						echo '<h1>Archival Projects Reference</h1>';
						echo '<table id="archivalprojectsTable">';
						echo '<tr><th>ID</th><th>Source URL</th><th>Scope of project</th><th>URL of archived data</th><th class="highlightedCell">Status of project</th></tr>';
						if(rq('editTable',true) == 'true') {
							echo $db->editTable("archives","archives");
						}
						else {
							echo $db->displayTable("archives","archives",array("status"=>"Done",));
						}
						echo '</table>';
						break;
					case 'encodings':
						echo '<h1>Data for known encodings</h1>';
						break;
				}
				endHtmlPage();
				$db->close();
			}
		}
		#Testing
		{
			function showTests() {
				createHtmlPage("Ember: Tests");
				echo '<div style="white-space:nowrap;">';
				echo 'Test results:<br>';
				echo runTests();
				echo '</div>';
				endHtmlPage();
			}
			function showHelloWorld() {
				$format = rq('format',true);
				$helloWorld = getHelloWorld($format);
				#help from http://webdesign.about.com/od/php/ht/force_download.htm
				$filename = 'HelloWorld_'.$format.'_Generated'.date('c');
				$filename = formatFilename($filename,$format);
				$length = strlen($helloWorld);
				header("Content-disposition: attachment; filename=".$filename);
				header("Content-type: application/octet-stream");
				header("Content-length: ".$length);
				echo $helloWorld;
			}
		}
		#Utility
		{
			function updateDatabaseFieldAPI() {
				$db = getDbObjectByName(rq('db'));
				$db->updateDatabaseField(rq('dataTargetTable'),rq('dataTargetRow'),rq('dataTargetColumn'),base64_decode(rq('dataValue')));
			}
			function addRowsToTableAPI() {
				$db = getDbObjectByName(rq('db'));
				$db->addRowsToTable(rq('table'),rq('copyRows'),rq('numberOfRows'));
			}
			function getConvertedDataAPI() {
				echo getConvertedDataFromRequest();
			}
			function downloadConvertedDataAPI() {
				$data = getConvertedDataFromRequest(false);
				$format = rq('outputFormat');
				$filename = 'Converted_'.$format.'_Generated'.date('c');
				$filename = formatFilename($filename,$format);
				$length = strlen($data);
				header("Content-disposition: attachment; filename=".str_replace("\n",'',$filename));
				header("Content-type: application/octet-stream");
				header("Content-length: ".$length);
				echo $data;
			}
		}
	}
}

# 3. Determine what I'm supposed to do
{
	$action = rq('action');
	if($action instanceof Exception) {
		showWelcomePage();
	}
	else {
		switch($action) {
			case "showIndex":
				showIndex();
				break;
			case "showWelcomePage":
				showWelcomePage();
				break;
			case "conversionUtility":
				conversionUtility();
				break;
			case "dcEditor":
				dcEditor();
				break;
			#Documentation
			case "showDocumentation":
				showDocumentation();
				break;
			case "showArchivalProjectsTable":
				showArchivalProjectsTable();
				break;
			case "showDceTable":
				showDceTable();
				break;
			#Testing
			case "showTests":
				showTests();
				break;
			case "showHelloWorld":
				showHelloWorld();
				break;
			#Utility
			case "updateDatabaseFieldAPI":
				updateDatabaseFieldAPI();
				break;
			case "addRowsToTableAPI":
				addRowsToTableAPI();
				break;
			case "getConvertedDataAPI":
				getConvertedDataAPI();
				break;
			case "downloadConvertedDataAPI":
				downloadConvertedDataAPI();
				break;
			default:
				resetEmber();
		}
	}
}
?>