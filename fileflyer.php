<?php
/**
 
 
 (¯`·.¸¸.·´¯`·.¸¸.-> Fileflyer INFO Get Script (Link Opener) V3 By Dowser <-.¸¸.·´¯`·.¸¸.·´¯)
 
 
This script just get the INFO of any FILEFLYER file using a FILEFLYER password.. But also works without a password.
You can use this for example to download files to your server and reupload to another one.
 
NOTICE! - Fileflyer use an IP based link so you cannot get a link with this script and then download it with another computer.
 
WARNING! - I am not responsible for any use made with this script.
 
INSTALL:
1.Upload this page
2.Edit this file - enter password in $password = "";
4.Use fileflyer.php?id=(Fileflyer ID)
5.Enjoy :)
 
Coded by Dowser
 
Please Report Bugs At Dowser [ at ] H0tm4il [ dot ] CoM
 
 Parameters (example):
 $fileflyer->Get("your fileflyer password","File Id","Get the link","Get the filename","Get the filesize")
 
**/
Class Fileflyer {
/**   Functions   **/
        public function potong($content,$start,$end){
                if($content && $start && $end) {
                        $r = explode($start, $content);
                                if (isset($r[1])){
                                        $r = explode($end, $r[1]);
                                        return $r[0];
                                }
                        return '';
                }
        }	
		public function GetDomain($url){
				$nowww = str_replace('www.','',$url);
				$domain = parse_url($nowww);
						if(!empty($domain["host"])){
								return $domain["host"];
						}else{
								return $domain["path"];
						}
		}
 
/**   Starting The Script   **/
		public function Get($password,$id,$link,$name,$size){
		if($id == "") return("Fatal Error: Please enter an ID for a Fileflyer file.");
		
		$url = "http://www.fileflyer.com/view/" . $id; // The Fileflyer Link
		$cookietime = time() + 100;
		$useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.76 Safari/537.36"; // The Useragent
		
		$ch = curl_init();
                curl_setopt($ch,CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$res = curl_exec($ch);
		curl_close($ch);
 
		$count = substr_count($res, '<table border="0" cellpadding="0" cellspacing="0" class="fileslist">');
		for ($i = 1; $i <= $count; $i++) {
		
		$list = explode('<table border="0" cellpadding="0" cellspacing="0" class="fileslist">', $res);
		
		$link = $this->potong($list[$i],'class="dwlbtn" href="http','"'); // Trying to get the link if dont need password
		$valid = $this->potong($res,'">To report a ',' ,'); // Trying to know if the file valid
		$expired = $this->potong($list[$i],'<span class="removed','><a'); // Trying to know if the file was expired
		$removed = $this->potong($list[$i],'<span class="removedlink"><a id="ItemsList_ctl00_','"'); // Trying to know if the file was removed
		$filename = $this->potong($list[$i],'file" title="','"');  // Getting the File name
		$minus = $i - 1;
        	$filesize = $this->potong($list[$i],'<span id="ItemsList_ctl0'.$minus.'_size">','<');  // Getting the File size
		$posturl = $this->potong($res,'action="','"');
			if($expired == 'link"') return("Fatal Error: Sorry but this file was expired.");
			if($valid == "bug") return("Fatal Error: Sorry but the file was not found.");
       			if($removed == "RemovedLink") return("Fatal Error: Sorry but the file was removed.");
			if($link == ""){ // Password Needed
			if($password == "") return("Fatal Error: Please enter a password.");
				
                        $viewstate = $this->potong($res,'"__VIEWSTATE" value="','"');
                        $eventvalidation = $this->potong($res,'id="__EVENTVALIDATION" value="','" />');
                        $fields = array(
                                'Password'=>$password,
                                'SMSButton'=>'Go',
                                '__VIEWSTATE'=>$viewstate,
                                '__EVENTVALIDATION'=>$eventvalidation,
                                '__EVENTTARGET'=>'',
                                '__EVENTARGUMENT'=>'',
                                'TextBox1'=>'',
                                'CheckBoxPass'=>'on',
                                );
                       
                        $fields_string = "";
                        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                        rtrim($fields_string,'&');
                        $fields_string = urlencode($fields_string);
						
						$ch = curl_init();		
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_URL, $posturl);
						curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
						curl_setopt($ch,CURLOPT_POST,count($fields));
						curl_setopt($ch, CURLOPT_REFERER, $url);
						curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
						$result = curl_exec($ch);
                        curl_close($ch);
						
                        $link = $this->potong($result,'class="dwlbtn" href="http','"');
                        if($link == ""){
				$return[$i]['link'] = "Please enter a valid password to get link";
                        }else{
				if($link == TRUE)
				{
					$return[$i]['link'] = "http" . $link;
				}		
                        }
				if($link == "" && $password != "")  return("Fatal Error: Maybe your Fileflyer password is not working or Fileflyer were blocked this script. Please contact the developer - Dowser.");
                }else{ // No Password Needed           
                            if($link == TRUE)
							{
								$return[$i]['link'] = "http" . $link;
							}
							if($link == ""){return("Fatal Error: Maybe your Fileflyer password is not working or Fileflyer were blocked this script. Please contact the developer - Dowser.");}	
                }
			if($name == TRUE)
			{
				$return[$i]['name'] = $filename;
			}
			if($size == TRUE)
			{
				$return[$i]['size'] = $filesize;
			}
		}
			return $return;
		}
}
/**   End Of The Script   **/

$password = "";
$id = $_GET['id'];

$fileflyer = New Fileflyer;

echo "<pre>";
print_r($fileflyer->Get($password,$id,1,1,1)); // Password - Id - Link - Filename - Filesize 
echo "</pre>";


?>
