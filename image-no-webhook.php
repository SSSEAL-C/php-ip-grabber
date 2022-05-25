<?php
      header('Content-Type: image/png');
      class OS_BR{
      
          private $agent = "";
          private $info = array();
          function __construct(){
              $this->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
              $this->getBrowser();
              $this->getOS();
          }
      
          function getBrowser(){
              $browser = array("Navigator"            => "/Navigator(.*)/i",
                               "Firefox"              => "/Firefox(.*)/i",
                               "Internet Explorer"    => "/MSIE(.*)/i",
                               "Google Chrome"        => "/chrome(.*)/i",
                               "MAXTHON"              => "/MAXTHON(.*)/i",
                               "Opera"                => "/Opera(.*)/i",
                               );
              foreach($browser as $key => $value){
                  if(preg_match($value, $this->agent)){
                      $this->info = array_merge($this->info,array("Browser" => $key));
                      $this->info = array_merge($this->info,array(
                        "Version" => $this->getVersion($key, $value, $this->agent)));
                      break;
                  }else{
                      $this->info = array_merge($this->info,array("Browser" => "UnKnown"));
                      $this->info = array_merge($this->info,array("Version" => "UnKnown"));
                  }
              }
              return $this->info['Browser'];
          }
      
          function getOS(){
              $OS = array("Windows"   =>   "/Windows/i",
                          "Linux"     =>   "/Linux/i",
                          "Unix"      =>   "/Unix/i",
                          "Mac"       =>   "/Mac/i"
                          );
      
              foreach($OS as $key => $value){
                  if(preg_match($value, $this->agent)){
                      $this->info = array_merge($this->info,array("Operating System" => $key));
                      break;
                  }
              }
              return $this->info['Operating System'];
          }
      
          function getVersion($browser, $search, $string){
              $browser = $this->info['Browser'];
              $version = "";
              $browser = strtolower($browser);
              preg_match_all($search,$string,$match);
              switch($browser){
                  case "firefox": $version = str_replace("/","",$match[1][0]);
                  break;
      
                  case "internet explorer": $version = substr($match[1][0],0,4);
                  break;
      
                  case "opera": $version = str_replace("/","",substr($match[1][0],0,5));
                  break;
      
                  case "navigator": $version = substr($match[1][0],1,7);
                  break;
      
                  case "maxthon": $version = str_replace(")","",$match[1][0]);
                  break;
      
                  case "google chrome": $version = substr($match[1][0],1,10);
              }
              return $version;
          }
      
          function showInfo($switch){
              $switch = strtolower($switch);
              switch($switch){
                  case "browser": return $this->info['Browser'];
                  break;
      
                  case "os": return $this->info['Operating System'];
                  break;
      
                  case "version": return $this->info['Version'];
                  break;
      
                  case "all" : return array($this->info["Version"], 
                    $this->info['Operating System'], $this->info['Browser']);
                  break;
      
                  default: return "Unkonw";
                  break;
      
              }
          }
      }
      function get_client_ip() {
          $ipaddress = '';
          if (isset($_SERVER['HTTP_CLIENT_IP']))
              $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
          else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
              $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
          else if(isset($_SERVER['HTTP_X_FORWARDED']))
              $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
          else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
              $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
          else if(isset($_SERVER['HTTP_FORWARDED']))
              $ipaddress = $_SERVER['HTTP_FORWARDED'];
          else if(isset($_SERVER['REMOTE_ADDR']))
              $ipaddress = $_SERVER['REMOTE_ADDR'];
          else
              $ipaddress = 'UNKNOWN';
          return $ipaddress;
      }
      function ip_location() {
        $ip = get_client_ip();
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $city = $details->city;
        $region = $details->region;
        $country = $details->country;
        
        return $city . ", " . $region . ", " . $country;
      }
      function actualurl() {
        $request_headers = apache_request_headers();
        return $request_headers['Referer'];
      }
      function coords() {
        $ip = get_client_ip();
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        return $details->loc;
      }
      function os() {
        $obj = new OS_BR();
        return $obj->showInfo('browser') . " " . $obj->showInfo('version') . " " . $obj->showInfo('os');
      }
      if (strpos(actualurl(),'id.repl.co') !== false) return;
      if (get_client_ip() === '208.115.199.27') return;
      if (substr(get_client_ip(), 0, 4) == "172." or substr(get_client_ip(), 0, 3) == "10." or substr(get_client_ip(), 0, 8) == "192.168.") return;
      $width = 80;
      $height = 300;
      $im = @imagecreate($height, $width)
          or die("Cannot Initialize new GD image stream");
      $background_color = imagecolorallocate($im, 0, 0, 0);
      $red = imagecolorallocate($im, 233, 14, 91);
      $yellow = imagecolorallocate($im,255,255,0);
      $green= imagecolorallocate($im, 0,255,0);
      $blue = imagecolorallocate($im, 0, 0, 255);
      $magenta = imagecolorallocate($im,150,0,255);
      $colr = imagecolorallocate($im,0,255,255);
      imagestring($im, 3, 0, 5,  get_client_ip(), $blue);
      imagestring($im, 1, 0, 25, ip_location(), $red);
      imagestring($im, 1, 0, 35, coords(), $green);
      imagestring($im, 1, 0, 45, os(), $yellow);
      imagestring($im,1,0,55,actualurl(),$colr);
      imagestring($im, 1, 0, 70, "seall.dev", $magenta);

      imagepng($im);
      imagedestroy($im);

?>
