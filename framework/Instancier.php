<?php
class Instancier {

    // Instanciate an class from his name
    // Only if class depend of nothing or another class
    function new($className) {
        $class = new ReflectionClass($className);
        $constructor = $class->getConstructor();

        if($constructor == null) { return new $className(); }

        $parameters = $constructor->getParameters();
        $arguments = [];

        // For each constructor parameter
        // Check if is a class, try to instanciate
        // Else throw error
        foreach($parameters as $parameter) {
            $type = $parameter->getType()->getName();
            $name = $parameter->getName();

            if(class_exists($type)) {
                $arguments[$name] = $this->new($type);
            } else {
                throw new Exception('Could not instanciate : All constructor parameters must be class instances.', 0);
            }
        }

        return $class->newInstanceArgs($arguments);
    }
}

?>