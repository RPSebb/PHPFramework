<?php
class Importer {
    public function importa($folder) {
        $filenames = scandir($folder);
        $name = [];
        foreach($filenames as $filename) {
            if(substr($filename, -4, 4) !== '.php') { continue; }
            include_once($folder.'/'.$filename);
            $name[] = substr($filename, 0, -4);
        }
        return $name;
    }

    public function import($folder) {
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