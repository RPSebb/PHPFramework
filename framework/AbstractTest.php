<?php
abstract class AbstractTest {
    protected $obj;

    public function __construct($obj = null) {
        $this->obj = $obj;
    }

    protected function assert(string $method, string $description, mixed $expected, mixed $args) : void {
        $output = call_user_func_array(array($this->obj,  $method),  $args);
        $success = $output === $expected;
        $this->printResult($description, $args, $expected, $output, $success);
    }

    protected function printResult($description, $args, $expected, $output, $success) : void {
        $description = $this->toJavascript($description);
        $args        = $this->toJavascript($args);
        $expected    = $this->toJavascript($expected);
        $output      = $this->toJavascript($output);
        $result      = $this->toJavascript($success ? 'Succed' : 'Failed');
        $color       = $success ? 'rgb(75,255,120)' : 'rgb(255,0,0)';
        echo("<script>
                console.log('%c' + $result, 'color: $color', '\\n' + $description, '\\nargs:', $args, '\\nexpected:', $expected, '\\noutput:  ', $output);
            </script>"
        );
    }

    private function toJavascript($value) : string {
        $number = ['integer', 'float'];
        $type = gettype($value);
        if(in_array($type, $number)) {
            return "Number($value)";
        } else if($type === 'boolean') {
           return "Boolean($value)";
        } else if($type === 'string') {
            return '"'.addslashes($value).'"';
        } else {
            return json_encode($value);
        }
    }
}
?>