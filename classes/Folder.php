<?php
/** Class directory handles all files and directory operations 
 * @property string $path Path to the directpry to be manipulated
 */
class Folder extends Directory {
    //Class properties/data
    private $path;
    
    //Class constructor
    /** Class directory handles all files and directory operations */
    public function __construct() {
    }
    
    //Using Magic__set and __get
    function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
    
    /** Method that fetches images form a given directory 
     * @param string $path Folder/Directory/File path or location of the images
     * @return Array String array of image paths
     */
    public static function getImages($path){
        $files  = scandir($path,1);//get all files
        $validExtention = array(".png",".jpg",".gif");//allowed file extension
        $images = array();//create image array
        //loop through retured file array
        foreach($files as $file ){
            if(in_array(strtolower(strrchr($file,".")), $validExtention)){$images[] = $file;}//return only image files
        }
        return $images;
    }
    
}
