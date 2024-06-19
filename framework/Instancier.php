<?php
class Instancier {

    /** Instanciate an class from its name
     * 
     * Can not instanciate primitive
     * 
     * If the class depends on primitive, an Exception will be throw
     */
    function new(string $className) : Object {
        $class = new ReflectionClass($className);
        $constructor = $class->getConstructor();

        if($constructor == null) { return new $className(); }

        $parameters = $constructor->getParameters();
        $arguments = [];

        // Check if its a class and instanciate it
        // Else throw error
        foreach($parameters as $parameter) {
            $type = $parameter->getType()->getName();
            $name = $parameter->getName();

            if(class_exists($type)) {
                $arguments[$name] = $this->new($type);
            } else {
                throw new Exception('Could not instanciate : All constructor parameters must be a class.', 0);
            }
        }

        return $class->newInstanceArgs($arguments);
    }
}

?>