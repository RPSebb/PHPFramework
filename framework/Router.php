<?php
class Router {

    public  array  $routes       = [];
    private array  $controllers  = [];
    private string $requestRoute = '';
    private        $instancier   = null;
    private        $importer     = null;

    public function __construct(Instancier $instancier, Importer $importer, Request $request) {
        $this->instancier    = $instancier;
        $this->importer      = $importer;
        $this->requestRoute  = $request->getRoute();
        $this->controllers   = $this->importer->import(__DIR__.'/../'.constant('controllerFolder'));
        $this->createRoutes();
        $this->matchRoute();
    }

    // Store object containing routes, methods, parameters
    private function createRoutes() {
        foreach($this->controllers as $controller) {
            $obj = new $controller();
            $reflection = new ReflectionObject($obj);
            foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                foreach($method->getAttributes('Route') as $attribute) {
                    $args   = $attribute->getArguments();
                    $output = array(
                        'httpMethods'=> $args['httpMethods'],           // Array of http methods        Ex: ['GET'] or ['PUT', 'PATCH'] etc...
                        'value'      => $args[0],                       // Raw route                    Ex: /person/{id}
                        'pattern'    => '',                             // Pattern route                Ex: /person/{id}, ['DELETE'] => /^(?:delete)#\/person\/([^\/]+)\/?$/
                        'className'  => $controller,                    // Class name of the controller Ex: PersonController
                        'method'     => $method->getName(),             // Associated method            Ex: delete
                        'parameters' => $method->getParameters(),       // Parameters of the method     Ex: PDODatabase $db, $id
                        'order'      => []                              // Names of route parameters in order
                    );                                                  // Ex: /get/person/{id}/books/{bookId}  => [id, bookId]
                                                                        // Ex: /author/{name}/books/{reference} => [name, reference]
                    // Escape '/'
                    $route  = addcslashes($args[0], '/');

                    // Combine array to string and lower case Ex: ['PUT', 'PATCH'] => put|patch
                    $h = strtolower(implode('|', $args['httpMethods']));

                    // Ex: ['GET'], /person/{id}/books/{bookId} => /^(?:get)#\/person\/{id}\/books\/{bookId}\/?$/
                    $pattern = "/^(?:$h)#$route\/?$/";
                    $output['pattern'] = $pattern;

                    // Replace pattern
                    $r = '([^\/]+)';

                    // Replace {value} by ([^\/]+)
                    // Store content between {} in array with its position
                    // Ex: /^(?:get)#\/person\/{id}\/books\/{bookId}\/?$/ become /^(?:get)#\/person\/([^\/]+)\/books\/([^\/]+)\/?$/
                    // And store id and bookId in an array matching their position in the route [id, bookId]
                    if(preg_match_all('/(?:\{.+?\})/', $route, $matches)) {
                        foreach($matches as $match) {
                            // Replace value in {}
                            $output['pattern'] = str_replace($match, $r, $pattern);

                            // Store each parameters from route pattern
                            foreach($match as $value) { array_push($output['order'], substr($value, 1, -1)); }
                        }

                        // Push route without parameters to the top
                        array_push($this->routes, $output);
                    } else {
                        // Push route with parameters to the bottom
                        array_unshift($this->routes, $output);
                    }
                }

            }
        }
    }

    // Call method of the controller associated with the requestURI
    private function matchRoute() {
        foreach($this->routes as $route) {

            // Does requestURI match the route pattern
            if(preg_match_all($route['pattern'], $this->requestRoute, $matches)) {
                $controller = new $route['className']();
                $method = $route['method'];
                $args = [];

                // Fill args as args[parameterName] = value
                foreach($route['parameters'] as $parameter) {
                    $type = $parameter->getType();
                    $name = $parameter->getName();

                    // Is parameter not a class
                    if($type == null) {
                        // array_search return false or position (bool or int)
                        $position = array_search($name, $route['order'], true);
                        if($position !== false) {
                            // Retrieve value from matches
                            $args[$name] = $matches[$position + 1][0]; 
                        }
                    } else {
                        // Instanciate from type name
                        $args[$name] = $this->instancier->new($type->getName());
                    }
                }

                return call_user_func_array(array($controller, $method), $args);
            }
        }

        // If we get out of for loop, no mathing route was founded
        throw new Exception("Route does not exist", 404);
    }
}
?>