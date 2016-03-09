<?php
class WebPage {
    
    public function WebPage() {
        //includes predefined constants
        include(CONST_FILE_PATH); 
        //Include Databse manipulation handler file
        include(DB_CONFIG_FILE); 
        
        function autoLoadClasses($className){
            $path = CLASSES_PATH;
            $ext = ".php";
            $fullpath = $path.sprintf("%s",$className.$ext);
            if(file_exists($fullpath)){
                return include $fullpath;
            }else{
                echo $fullpath;
            }
        }
        spl_autoload_register('autoLoadClasses');
    }
    
    /** Custom message box */
    public static function messageBox($message,$type){
	switch($type){
            case 'error':   $class = 'alert-danger'; break;
            case 'success': $class = 'alert-success'; break;
            case 'info':    $class = 'alert-info'; break;
            case 'warning': $class = 'alert-warning'; break;
        }
        //$html = file_get_contents(TEMPLATE_PATH.'HTML/forms-error-box.html');
        $html = '<div class="alert '.$class.'"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$message.'</div>';
        return $html;
    }
    
    /** Method for dispalying error message  */
    function showError($error){
        if(is_array($error)){
            $msg ="<p>Please attend to the following errors:</p><ul>";
            foreach($error as $error){ $msg .="<li>".$error."</li>"; }     
            $msg .="</ul>";
        }
        else{
            $msg = $error;
        }
        return $this->messageBox($msg, 'error');
    }
    
    /** Redirect() redirects a webpage to $redirectTo
     *  @param string $location String path of the page to be redirected to
     */
    public function redirectTo($location){
       header("location: ".$location);exit;
   }
}
