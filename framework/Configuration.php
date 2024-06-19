<?php
class Configuration {
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