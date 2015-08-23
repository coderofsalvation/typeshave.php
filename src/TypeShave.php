<?
/**
 * <pre>
 * TypeShave.php
 *
 * The author was to busy coding to rewrite these lines.
 *
 * Usage example: 
 *   <code>  
 *     // some code
 *   </code>
 *
 * Changelog:
 *   [leon@Sun Aug 23 15:15:00 CEST 2015] initial version
 * </pre>
 *
 * @date Sun Aug 23 15:15:00 CEST 2015 
 * @version 0.5 
 * @author Leon van Kammen <info@2webapp.com>
 * @copyright 2015 2webapp.com
 * @abstract
 * @package yourpackage  
 * @todo  
 * @link  
 * @license BSD 
 */
namespace coderofsalvation;

include_once( __DIR__."/../vendor/geraintluff/jsv4/jsv4.php" );
 
class TypeShave {

  public static $schemaFiles = array();
  
  public static function sanitizeJson($str){
    return preg_replace('~[\r\n]+~', '', trim($str) );
  }

  /**
   * getSchema tries to an jsonschema objectstructure from jsonstring, php object or jsonschema filename
   * 
   * @param mixed $schema 
   * @static
   * @access public
   * @return void
   */
  public static function getSchema($schema){
    if( !is_object($schema) ){
      if( $schema[0] != "{" && !isset(self::$schemaFiles[$schema]) && file_exists($schema) ){
        $schema = self::$schemaFiles[$schema] = file_get_contents($schema);
      }else{ 
        if( isset(self::$schemaFiles[$schema]) ) $schema = self::$schemaFiles[$schema];
      }
      $schemaJson = self::sanitizeJson( $schema );
      $schema = json_decode($schemaJson);
      if( !$schema ){
        print_r($schemaJson);
        throw new Exception("corrupt jsonschema");
      }
    }
    return $schema;
  }

  /**
   * completeSchema fixes incomplete jsonschema definations
   * 
   * @param mixed $schema 
   * @param mixed $args 
   * @static
   * @access public
   * @return void
   */
  public static function completeSchema($schema,$args){
    if ( !isset($schema->type) ){
      $schema = (object)array(
        "type" => "object",
        "required" => "all",
        "properties" => $schema
      );
    }
    if( isset($schema->required) && $schema->required == "all" && isset($schema->properties) ){
      $required = array(); $namedargs = array();
      foreach( $schema->properties as $k => $v ){
        $namedargs[$k] = is_array($args) ? array_shift($args) : $args;
        $required[] = $k;
      } 
      $args             = (object)$namedargs;
      $schema->required = $required;
    }
    return (object)array( "schema"=>$schema, "args"=>$args);
  }

  /**
   * check 
   * 
   * @param mixed $args 
   * @param mixed $schema 
   * @static
   * @access public
   * @return void
   */
  public static function check($args, $schema = false){
    $btrace = debug_backtrace();
    $location = $btrace[0]['file'].":".$btrace[0]['line'];
    $schema = self::getSchema($schema);
    $result = self::completeSchema($schema,$args);
    $schema = $result->schema;
    $args = $result->args;
    $result = Jsv4::validate( (object)$args, $schema );
    if( $result->errors  ){
      if( !getenv("DEBUG") ){
        foreach( $result->errors as $k => $v ){
          trigger_error($location."\ntypeshave: ".preg_replace("/\//",".", $v->schemaPath) . " : " . $v->message,E_USER_WARNING );
        }
      }else print_r( array("args"=> $args, "errors" => $result->errors ));
      throw new Exception("TYPESHAVE_FAIL");
    }
  }
}

?>
