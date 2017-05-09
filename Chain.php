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

        $ret=system($cmd);
        return json_decode($ret, true);  
    }
    
    function getInfo(){
        return $this->connectToChain('getinfo', '');
    }
    
    function createUser($name){
        
        $params = '"stream", "'.$name.'", false';
        return $this->connectToChain('create', $params);
    }
    
    function listUsers(){
        $array = $this->connectToChain('liststreams', '');
        $ret = array();
        $results = $array["result"];
        foreach ($results as $value) {
            array_push($ret, $value["name"]);
        }
        return $ret;
    }
    
    function rateUser($toBeRated, $ratedBy, $payload){
        $hRatedBy = bin2hex($ratedBy);
        $hPayload = bin2hex($payload);
        $params = '"'.$toBeRated.'", "'.$hRatedBy.'", "'.$hPayload.'"';
        return $this->connectToChain('publish', $params);
    }
    
    function checkRating(){
        
    }
}

$chain = new Chain(multichainrpc, DFzHtvtTzJaPwCaWzTE1eG2KqQzEWwiQH9rxrFTTyPAQ, localhost, 5000);
$array = $chain->rateUser('bryan5', 'bob', '5,I like this store');
?>

