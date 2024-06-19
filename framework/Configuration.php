<?php
class Configuration {

    /** Try to open an .env file and store all contained values
     * 
     * The priority is .env.local -> .env -> .env.dev -> .env.prod
     * 
     * The research will stop at the first file founded
     */
    public function __construct() {
        $this->getServerInformations();
        $names = array('.env.local', '.env', '.env.dev', '.env.prod');

        foreach($names as $name) {
            $path =  __DIR__."/../$name";
            if(is_file($path)) {
                $ini = parse_ini_file($path);
                $keys = array_keys($ini);
                foreach($keys as $key) { define($key, $ini[$key]); }
                return;
            }
        }

        throw new Exception('Configuration file not founded', 0);
    }

    /** Usefull informations for request creation */
    private function getServerInformations() {
        $scriptFolder = preg_replace('/\/[^\/]*$/', '', $_SERVER['SCRIPT_NAME']);
        $server = $_SERVER['SERVER_NAME'];
        $protocol = 'http';
        if(isset($_SERVER['HTTP']) && $_SERVER['HTTP'] === 'on' ) {
            $protocol .= 's';
        }
        define('server', $protocol.'://'.$server);
        define('indexFolder', $scriptFolder);
        define('indexAddress', $protocol.'://'.$server.$scriptFolder);
    }
}
?>