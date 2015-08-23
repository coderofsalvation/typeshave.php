<? 

include_once( __DIR__."/../src/TypeShave.php" );

/*
 * foo2 : check single arg (jsonschema-style)
 * foo  : check all args at once
 */

function foo2($foo){
  TypeShave::check( $foo, '{ "foo": { "type": "string", "minLength":1, "regex": "/foo/"}  }' );
}

function foo($foo, $bar){
  TypeShave::check( func_get_args(), '{
    "foo": { "type": "string"  },
    "bar": { "type": "integer" }
  }');
  // do stuff
}

// test it 

foo( "this is string", 234 );
foo2( "this is stringgggggg" );

$ok = false;
try {
  print("expecting warning on following line: \n");
  foo( true, true );
}catch (Exception $e){
  $ok = true;
  print("OK");
}
if(  !$ok ) throw new Exception("NOT_OK");

/*
 * or just pass native php (bit more verbose though)
 */

function native($foo){
  TypeShave::check( $foo, (object)array( 
    'foo' => (object)array( 'type' => 'string' )
  ));
}

native("some string");

/*
 * phat(): test phat containers with seperate jsonschea file
 * perfect when the jsonstring gets too verbose: use a separate jsonfile
 */

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

$container->records["fffffffffffffo"] = "illegal";
$ok = false;
try{
  phat($container);
} catch (Exception $e){
  print("OK");
  $ok = true; 
}
if( !$ok  ) throw new Exception("NOT_OK");

?>
