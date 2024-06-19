<?php
class Request {

    /** Return an array of column name's from request header
     * 
     * @return array<string>
     * 
     * @exemple
     * request : GET http://domain.com/person/?columns=id,last_name,first_name
     * 
     * output : ['id', 'last_name', 'first_name']
     * */
    public function getColumns() : array {
        if(empty($columns = $this->get('columns'))) { return []; }
        return explode(',', preg_replace('/\s+/', '', $columns));
    }

    /** Return the value of a get parameter from his name
     * 
     * @param string $name parameter name
     * 
     * @exemple
     * request : GET http://domain.com/person/?tik=tok&pich=net
     * 
     * name : 'tik'
     * 
     * output : 'tok' */
    public function get(string $name = '') : string {
        if(!isset($_GET[$name])) { return ''; }
        return trim($_GET[$name]);
    }

    /** Assign value of a string array to an array
     * 
     * Existing array values will not be removed
     * 
     * @exemple:
     * 
     * input : "person[0][4]=50"
     * 
     * keys : ['person', '0', '4']
     * 
     * value : 50
     * 
     * output : array['person']['0']['4'] = 50
     */
    private function assignValue(array &$output, string $column, mixed $value) : void {
        if(preg_match_all('/^(\w+)|\[([a-zA-Z0-9]+)\]/', $column, $matches) === 3) {
            $matches[2][0] = $matches[1][0];
            $current = &$output;
            $lastKey = array_pop($matches[2]);

            foreach ($matches[2] as $key) {
                if (!isset($current[$key])) { $current[$key] = []; }
                $current = &$current[$key];
            }
            $current[$lastKey] = $value;
        } else {
            $output[$column] = $value;
        }
    }

    /** Try to transform json data from body into an array
     * 
     * Return empty array on fail or if data is not a json
     */
    public function formBody() : array {
        $data = json_decode(file_get_contents('php://input'), true);
        if(empty($data)) { return []; }

        $this->cleanArray($data);
        return $data;
    }

    /** Try to transform a form data into an array
     * 
     *  Return empty array on fail
     * 
     */
    public function formData() : array {
        try {
            $output = [];
            $file = @fopen('php://input', 'r');
            $line = 3;
            $column = '';
            if($file === false) { throw new Exception('Can not open file'); }
            while($data = @fgets($file, 1024)) {
                if($data === false) { throw new Exception('Can not read file'); }

                $res = preg_split('/:/', $data, -1, PREG_SPLIT_NO_EMPTY)[0];

                if($res === 'Content-Disposition') {
                    $res = preg_split('/=/', $data, -1, PREG_SPLIT_NO_EMPTY);
                    $column = trim(array_pop($res), " \r\n\"");
                    $line = 0;
                }

                if($line === 2) {
                    $value = htmlspecialchars(trim($data, " \r\n"), ENT_NOQUOTES, 'UTF-8');
                    $this->assignValue($output, $column, $value);
                }

                $line++;
            }
        }
        catch (Throwable $th) {} 
        finally { if(is_resource($file)) { fclose($file); } return $output; }
    }

    /** Try to transform a xxx-w-form-urlencoded data into an array
     * 
     *  Return empty array on fail
     * 
     */
    public function formUrlEncoded() : array {
        $keyValues = preg_split('/&/', urldecode(file_get_contents('php://input')));
        $output = [];
        foreach($keyValues as $keyValue) {
            $res = preg_split('/=/', $keyValue, -1, PREG_SPLIT_NO_EMPTY);
            if(count($res) !== 2) { return []; }

            $column = trim($res[0]);
            $value  = htmlspecialchars(trim($res[1], "\r\n"), ENT_NOQUOTES, 'UTF-8');

            $this->assignValue($output, $column, $value);
        }

        return $output;
    }
    
    /** Return all data from request form or body as an array */
    public function postData() : array {
        $data = $_POST;
        if(empty($data)) { 
            $data = json_decode(file_get_contents('php://input'), true);
        }

        if(empty($data)) { return []; }

        $this->cleanArray($data);
        return $data;
    }

    /** Trim and html escape recursively all values inside an array */
    private function cleanArray(array &$input) : void {
        foreach($input as &$value) {
            switch (gettype($value)) {
                case 'string':
                    $value = htmlspecialchars(trim($value), ENT_NOQUOTES, 'UTF-8');
                    break;
                case 'array':
                    $this->cleanArray($value);
                    break;
                default:
                    break;
            }
        }
    }

    /** Return the requested route base on the index.php location
     * @exemple 
     * project_folder : /www/projectNeon/index.php
     * 
     * request : GET http://domain.com/projectNeon/person
     * 
     * output : get#/person 
    */
    public function getRoute() : string {
        return strtolower($_SERVER['REQUEST_METHOD'])."#".substr($_SERVER['REDIRECT_URL'], strlen(constant('indexFolder')));
    }
}
?>