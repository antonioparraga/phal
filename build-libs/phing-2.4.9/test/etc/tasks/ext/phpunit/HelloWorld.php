<?php

    /**
     * The Hello World class!
     *
     * @author Michiel Rook
     * @version $Id: HelloWorld.php 1351 2011-11-01 10:14:52Z mrook $
     * @package hello.world
     */
    class HelloWorld
    {
        public function foo($silent = true)
        {
            if ($silent) {
                return;
            }
            return 'foo';
        }

        function sayHello()
        {
            return "Hello World!";
        }
    };

?>
