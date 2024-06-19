<?php
class ArrayVisualizer {

    function print($arr, $depth = 0) {
        $filled = !empty($arr);
        $keys = array_keys($arr);
        if($filled) { str_repeat('&emsp;', $depth); }
        echo('{');
        if($filled) { echo('<br>'); }
        foreach($keys as $key) {
            $value = $arr[$key];
            echo(str_repeat('&emsp;', $depth + 1).$key.' => ');
            if(is_array($value)) { 
                $this->print($value, ++$depth);
                $depth--;
            }
            else {
                try {
                    echo($value.'<br>');
                } catch (Throwable $th) {
                    echo(var_dump($value).'<br>');
                }
            }
        }
        if($filled) { str_repeat('&emsp;', $depth); }
        echo(str_repeat('&emsp;', $depth).'}<br>');
    }

    function json(array $arr) : string {
        $text = '{';
        $lastKey = array_key_last($arr);

        foreach($arr as $key => $value) {
            $text .= "\"$key\"" . ' : ';
            if(is_array($value)) {
                $text .= $this->json($value);
            } else {
                try {
                    $text .= "\"$value\"";
                } catch (\Throwable $th) {
                    $text .= 'unknown';
                }
            }

            if($key !== $lastKey) { $text .= ','; }
        }

        $text .= '}';
        return $text;
    }
}
?>