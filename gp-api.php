<?php
	require_once 'PhpRestClient/SimpleRestClient.php';
	require_once 'util.php';
	
	class GpAPI {
		private $baseurl = null;
		private $restclient = null;
		private $format = null;
		
		public static function gphash($str) {
			$command = PERL.' '.PERL_LIB.'/sha512_base64.pl '.$str;
			$result = exec($command);
			return $result;
		}
		
		public function __construct($baseurl = DEFAULT_API, $format='json') {
			$this->baseurl = $baseurl;
			$cert_file=null;//Path to cert file 
			$key_file=null;//Path to private key
			$key_password=null;//Private key passphrase
			$curl_opts=null;//Array to set additional CURL options or override the default options of the SimpleRestClient
			$post_data=null;//Array or string to set POST data 
			$user_agent = 'mGP';
			$this->restclient = new SimpleRestClient($cert_file, $key_file, $key_password, $user_agent, $curl_opts);
			$this->format = $format;
		}
		
		public function handleError($code) {
			echo 'Error: '.$code.'<BR>';
		}
		
		public function Authenticate($username, $password) {
			$hashedpw = GpAPI::gphash($password);
			
			
			$url = $this->baseurl."Authenticate?UserName=$username&Password=$hashedpw&format=".$this->format;
			$this->restclient->getWebRequest($url);
			if($this->restclient->getStatusCode() == 200) {
				return json_decode($this->restclient->getWebResponse(), TRUE);
			}
			else {
				$this->handleError($this->restclient->getStatusCode());
			}
			return null;
		}
		
		private function __handle_url($url) {
			$this->restclient->getWebRequest($url);
			if($this->restclient->getStatusCode() == 200) {
				return json_decode($this->restclient->getWebResponse(), TRUE);
			}
			else {
				$this->handleError($this->restclient->getStatusCode());
			}
			return null;
		}
		
		public function Posts($tid='',$pid='',$tlimit='') {
			$username = $_SESSION['username'];
			$pwhash = $_SESSION['pwhash'];
			$url = $this->baseurl."Posts?UserName=$username&Password=$pwhash&format=".$this->format;
			if(!empty($tid)) {
				$url .= '&ThreadID='.$tid;
			}
			if(!empty($pid)) {
				$url .= '&PostID='.$pid;
			}
			if(!empty($tlimit)) {
				$url .= '&ThreadLimit='.$tlimit;
			}
			return $this->__handle_url($url);
		}
		
		public function PostGet($pid) {
			$username = $_SESSION['username'];
			$pwhash = $_SESSION['pwhash'];
			$url = $this->baseurl."PostGet?UserName=$username&Password=$pwhash&format=".$this->format;
			if(!empty($pid)) {
				$url .= '&PostID='.$pid;
			}
			return $this->__handle_url($url);
		}
		
		public function PostAdd($postid, $subject, $description) {
			$description = urlencode($description);
			$subject = urlencode($subject);
			$username = $_SESSION['username'];
			$pwhash = $_SESSION['pwhash'];
			$url = $this->baseurl."PostAdd?UserName=$username&Password=$pwhash&format=".$this->format;
			$url .= "&Subject=$subject&Description=$description";
			if(!empty($postid)) {
				$url .= '&ReplyToID='.$postid;
			}
			return $this->__handle_url($url);
		}
		
		public function PostMarkAs($read, $threadid, $postid) {
			$username = $_SESSION['username'];
			$pwhash = $_SESSION['pwhash'];
			$url = $this->baseurl."PostMarkAs?UserName=$username&Password=$pwhash&format=".$this->format;
			
			if($read) {
				$url .= '&Read=True';
			}
			else {
				$url .= '&Read=False';
			}
			
			if(!empty($threadid)) {
				$url .= '&ThreadID='.$threadid;
			}
			else if(!empty($postid)) {
				$url .= '&PostID='.$postid;
			}
			return $this->__handle_url($url);
		}
		
		public function PostCountUnread() {
			$username = $_SESSION['username'];
			$pwhash = $_SESSION['pwhash'];
			$url = $this->baseurl."PostCountUnread?UserName=$username&Password=$pwhash&format=".$this->format;
			return $this->__handle_url($url);
		}
	}
?>