<!--Chain.php
#Interface for Multichain database-->

<?php
class Chain{
    public $ipaddress;
    public $rpcport;
    public $username;
    public $password;
    
    function __construct($username, $password, $ipaddress = 'localhost', $rpcport) {
        $this->username = $username;
        $this->password = $password;
        $this->ipaddress = $ipaddress;
        $this->rpcport = $rpcport;
    }
    
    function connectToChain($method, $params){
        $cmd = 'curl -s --user '.$this->username.':'.$this->password.' --data-binary \''
                . '{"jsonrpc": "1.0", "id":"curltest", "method": "'.$method.'", "params": ['
                .$params
                .'] }\' -H "content-type:text/plain;" http://'.$this->ipaddress.':'.$this->rpcport.'/';

        $ret=exec($cmd);
        //$ret = system($cmd);
        return json_decode($ret, true);  
    }
    
    function getInfo(){
        return $this->connectToChain('getinfo', '');
    }
    
    function createUser($name){
        $hname = bin2hex($name);
        $params = '"stream", "'.$hname.'", false';
        return $this->connectToChain('create', $params);
    }
    
    function listUsers(){
        $array = $this->connectToChain('liststreams', '');
        $ret = array();
        $results = $array["result"];
        foreach ($results as $value) {
            array_push($ret, hex2bin($value["name"]));
        }
        return $ret;
    }
    
    function rateUser($toBeRated, $ratedBy, $payload){
        $htoBeRated = bin2hex($toBeRated);
        $hRatedBy = bin2hex($ratedBy);
        $hPayload = bin2hex($payload);
        $params = '"'.$htoBeRated.'", "'.$hRatedBy.'", "'.$hPayload.'"';
        return $this->connectToChain('publish', $params);
    }
    
    function checkRating($userName){
        $huserName = bin2hex($userName);
        $this->connectToChain('subscribe', '"'.$huserName.'"');
        $array = $this->connectToChain('liststreamitems', '"'.$huserName.'"');
        $ret = array();
        $results = $array["result"];
        foreach ($results as $value) {
            array_push($ret, array(hex2bin($value["key"]), hex2bin($value["data"])));
        }
        return $ret;
    }
}

//$chain = new Chain(multichainrpc, DFzHtvtTzJaPwCaWzTE1eG2KqQzEWwiQH9rxrFTTyPAQ, localhost, 5000);
//$array = $chain->checkRating('bryan6');
//print_r($array);
?>

