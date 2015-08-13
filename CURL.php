<?

class Curly {
    private $curl;
    private $timeout;

    public function __construct() {
        $this->curl     = curl_init();
        $this->timeout  = 5;

        /* Defaults
         * =============================================== */
        $this->setReturnTransfer(1);
        $this->setConnectTimeOut($this->timeout); 

    }

    public function setURL($url) {
        curl_setopt($this->curl, CURLOPT_URL, $url);
    }

    public function setReturnTransfer($val) {
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $val);
    }

    public function setConnectTimeOut($val) {
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $val);
    }

    public function getURL($url) {
        $this->setURL($url);
        $return_data    = curl_exec($this->curl);
        curl_close($this->curl);
        return $return_data;
    }
}
