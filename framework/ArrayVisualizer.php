<?php
class ArrayVisualizer {

    protected $space = '&emsp;';
    protected $jl = '<br>';

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

    // function convert(array $arr, int $depth = 0) : string {
    //     $text = '{'.$this->jl;
    //     $lastKey = array_key_last($arr);

    //     foreach($arr as $key => $value) {
    //         $text .= str_repeat($this->space, $depth + 1) . "$key" . ' : ';
    //         if(is_array($value)) {
    //             $text .= $this->convert($value, $depth + 1);
    //             $jump = false;
    //         } else {
    //             $jump = true;
    //             try {
    //                 $text .= $value;
    //             } catch (\Throwable $th) {
    //                 $text .= 'unknown';
    //             }
    //         }

    //         if($key !== $lastKey) { $text .= ','; }
    //         if($jump) { $text .= $this->jl; }

    //     }

    //     $text .= str_repeat($this->space, $depth). '}' . $this->jl;
    //     return $text;
    // }

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