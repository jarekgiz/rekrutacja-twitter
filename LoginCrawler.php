<?php 
class LoginCrawler {
	private $protocol = 'http';
	private $host;
    private $port;
    private $user_agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36';
    private $ch;

	public function __construct($protocol = null, $host = null, $port = null) {
        if ($protocol != null)
            $this->protocol = $protocol;
        if ($host != null)
            $this->host = $host;
        if ($port != null)
            $this->port = $port;
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
    }

    public function __destruct() {
        curl_close($this->ch);
    }

	public function get_login_page($uri) {
		//https://twitter.com/login
        $url = 'https://'.$this->host.($this->port?':'.$this->port:'').'/login';
        $data = $this->send_data($url);
        
        return $data;
    }

    public function login($uri, $login, $password, $authenticity_token) {
		//https://twitter.com/sessions
        $url = $this->protocol.'://'.$this->host.($this->port?':'.$this->port:'').$uri;
        $fields = array(
            'session[username_or_email]' => $login,
            'session[password]' => $password,
            'return_to_ssl' => true,
            'scribe_log' => '',
            'redirect_after_login=' => '%2F', 
            'authenticity_token' => $authenticity_token
        );
        $headers = array("Content-type: application/x-www-form-urlencoded");
        $data = $this->send_data($url, 'post', $fields, $headers);
        
        return $data;
    }

    public function redirect_after_login($uri) {
        //https://twitter.com/sessions
        $url = $this->protocol.'://'.$this->host.($this->port?':'.$this->port:'').$uri;

        $data = $this->send_data($url, 'get');
        
        return $data;
    }

	private function send_data($url, $method = 'get', $fields = array(), $headers = array()) {

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, realpath("./tmp/cookie.txt"));
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, realpath("./tmp/cookie.txt"));
        curl_setopt($this->ch, CURLOPT_REFERER, $url);
        if ($method == 'post' && is_array($fields) && count($fields) > 0) {
            $fields_string = http_build_query($fields);
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->ch, CURLOPT_POST, true);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields_string); 
        }
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        }
        $html = curl_exec($this->ch);

        $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $redirect = curl_getinfo($this->ch, CURLINFO_REDIRECT_URL);
        $data = array('source' => $html, 'httpcode' => $httpcode, 'redirect' => $redirect);

        return $data;
    }
}