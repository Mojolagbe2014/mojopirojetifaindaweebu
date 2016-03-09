<?php
/**
 * Description of StringManipulator
 *
 * @author Kaiste
 */

/** It handles string manipulation */
abstract class StringManipulator {
    
    /** It gets the first few words delimited by 
     * @param mixed $input String to be truncated
     * @param int $numWords Number of words to be left 
     * @return string String truncated $input
     */
    public static function wordTruncate($input, $numWords) {
        if(str_word_count($input,0)>$numWords) {
            $WordKey = str_word_count($input,1);
            $WordIndex = array_flip(str_word_count($input,2));
            return substr($input,0,$WordIndex[$WordKey[$numWords]]);
        }
        else {return $input;}
    }
    
    /** Function that copies part of a string 
     * @param string $string String to be truncated
     * @param string $start String on which the copying will start and it is not included in the copied string
     * @param string $stop String on which the copying will stop and it is not included in the copied string
     * @return string Copied string from $string
     */
    public static function getPartOf($string, $start, $stop){ 
        $startPoint = strrpos($string, $start)+strlen($start);
        $endPoint = strrpos($string, $stop) - $startPoint;
        return substr($string, $startPoint, $endPoint);
    }
    
    /** mysqliPrep prepares the request parameter using mysqli_real_escape_string 
     * @param string $value Post variable or string to be prepared
     * @return string Prepared string
     */
    public static function mysqliPrep($value) {
        $magic_quotes_active = get_magic_quotes_gpc();
        $new_enough_php = function_exists("mysqli_real_escape_string"); // i.e. PHP >= v4.3.0
        if( $new_enough_php) {
            //Do nothing
        }
        else { //before PHP v4.3.0
            //if magic quotes aren't already on then add slashes manually
            if(!$magic_quotes_active) { $value = addslashes($value);}
            //if magic quotes are activ, then the slashes already exist
        }
        return $value;
    }
    
    /** function for validating if a array of strings is not empty 
     * @return Boolean True if all supplied arguments are not empty else false
     */
    public static function arrayNotEmpty(){
        foreach (func_get_args() as $arg) {
            if (empty($arg)) { array_push($arg); return false; } 
            else {continue; }
        }
        return true;
    }
    
    function mb_strlen($str, $encoding = 'iso-8859-1') {
        switch (str_replace('-', '', strtolower($encoding))) {
          case "utf8": return strlen(utf8_encode($str));
          case "8bit": return strlen($str);
          default:     return strlen(utf8_decode($str));
        }
    }
    function mb_substr($string, $start, $length = null, $encoding = 'iso-8859-1') {
        if ( is_null($length) )
          return substr($string, $start);
        else
          return substr($string, $start, $length);
    }
    function trimStringToFullWord($length, $string) {
        if (mb_strlen($string) <= $length) {
            $string = $string; //do nothing
        }
        else {
            $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length));
        }
        return $string;
    }
}
