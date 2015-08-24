typeshave
=========
Prevent functions from exploding with garbage-in garbage-out.

<center><img src="http://coderofsalvation.github.io/typeshave/logo.png"/></center> 

Guard your function's incoming data using typeshave wrappers in JS & PHP ([typeshave website](http://coderofsalvation.github.io/typeshave/)).

## Usage

    composer require coderofsalvation/typeshave

and then 

    use coderofsalvation\TypeShave;

    function foo2($foo){
      TypeShave::check( $foo, (object)array(
        "foo" => (object)array( "type" => "string", "minLength" => 1, "regex" => "/foo/" )
      ));
    }

    foo2( 123 );       // will throw exception 

or heck, we can even write jsonschema inline:
    
    function foo2($foo){
      TypeShave::check( $foo, '{ "foo": { "type": "string", "minLength":1, "regex": "/foo/"}  }' );
    }


> typeshave uses the established [jsonschema](http://jsonschema.net) validation-format, which even 
supports validating nested datastructures. Re-usable in many other areas (database-, restpayload-, form-validation and so on)

or

    // multiple arguments at once 

    function foo($foo, $bar){
      TypeShave::check( func_get_args(), '{
        "foo": { "type": "string"  },
        "bar": { "type": "integer" }
      }');
      // do stuff
    }

    foo( true, true ); // will throw exception

or how about passing PHAT nested containers using [separate jsonschema file](test/test.json) without getting verbose

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

> see [test.json here](https://github.com/coderofsalvation/typeshave.php/blob/master/test/test.json)

## why non-typesafe is great, but not with PHAT nested objects

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
* typesafe nested datastructures 
* verbose unittests doing typesafe stuff 

Typeshave deals with problems immediately when they occur to prevent this:

<center><img src="http://www.gifbin.com/bin/102009/1256553541_exploding-trash.gif"/></center>

## NOTE: arrays 

the v4 jsonschema validator does support arrays by noticing the 'items'-variable, so please omit `type: "array"` which can 
be found in older jsonschema formats:

       "myarray": {
    //   "type": array,     *REMOVE ME*
         "items": [{
            "type": "integer"  
          }]
       }

