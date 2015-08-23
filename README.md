typeshave
=========
Prevent functions from exploding with garbage-in garbage-out.

<center><img src="http://coderofsalvation.github.io/typeshave/logo.png"/></center> 

Guard your function's incoming data using typeshave wrappers in JS & PHP ([typeshave website](http://coderofsalvation.github.io/typeshave/)).

## Usage

    composer require coderofsalvation/typeshave

and then 

    use coderofsalvation/TypeShave;

    // function with single arg typeshave check 

    function foo2($foo){
      TypeShave::check( $foo, '{ "foo": { "type": "string", "minLength":1, "regex": "/foo/"}  }' );
    }

    foo2( 123 );       // fail please

> typeshave uses the established [jsonschema](http://jsonschema.net) validation-format. Re-usable 
in many other areas (database-, restpayload-, form-validation and so on)

or

    use coderofsalvation/TypeShave;

    // function with multiple arg typeshave check 

    function foo($foo, $bar){
      TypeShave::check( func_get_args(), '{
        "foo": { "type": "string"  },
        "bar": { "type": "integer" }
      }');
      // do stuff
    }

    foo( true, true ); // fail please 

or just pass native php (bit more verbose though)

    function native($foo){
      TypeShave::check( $foo, (object)array( 
        'foo' => (object)array( 'type' => 'string' )
      ));
    }

    native("some string"); // should pass

or how about passing PHAT containers using [separate jsonschema file](test/test.json) without getting verbose

    function phat($container){
      TypeShave::check( $container, __DIR__."/test.json" ); 
    }

    $container = (object)array(
      "foo" => "foo",
      "bar" => "bar2",
      "records" => array(
        (object)array( "foo" => "foo" )
      )
    );

    phat($container);

## why non-typesafe is great, but not with PHAT objects

For example:

* REST payloads 
* objects which represent configs or options 
* datastructures and resultsets for html-rendering or processing purposes

Are you still passing phat data around `fingers-crossed`-style?
Still wondering why functions like this explode once in a while? :D

    foo( (object)array( "foo"=>"bar", "bar"=>123, "records": array( 1, 2 )) );

Did you you try PITA-fying your code with if/else checks?

    function foo($data){
      if( isset($data)          && 
          is_object($data)      && 
          isset($data->foo)     && 
          is_string($data->foo) &&
          .. 
          && 
          .. 
          && Argh this is a big PITA 
      // omg how do I even check properties recursively?
      foreach( $data->records as $record ){
        // PITA 
        // PITA 
        // PITA 
        // PITA 
      }
      ...
      // now finally we can do what the function should do :/
    }

## Conclusion

No more :

* functions going out of control
* assertions-bloat inside functions 
* complaining about javascript not being typesafe
* unsafe recursive datastructures 
* verbose unittests doing typesafe stuff 

Typeshave deals with problems immediately when they occur to prevent this:

<center><img src="http://www.gifbin.com/bin/102009/1256553541_exploding-trash.gif"/></center>
