<?php
class Importer {

    /** Import all php file of a folder 
     * 
     * @return array list of files without .php extension
    */
    public function import(string $folder) {
        $handle = opendir($folder);
        $name = [];
        while(false !== ($entry = readdir($handle))) {
            if(substr($entry, -4, 4) !== '.php') { continue; }
            include_once($folder.'/'.$entry);
            $name[] = substr($entry, 0, -4);
        }
        closedir($handle);
        return $name;
    }
}
?>