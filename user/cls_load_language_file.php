<?php
class fill_language_file{   
    public static $shopify_lang_messages = array();
    public function __construct($lang = 'english', $shopify_file = 'form_validation_message') {
        
        $this->settle_lang_message();
    } 
    public function receive_lang_message(){
        return self::$shopify_lang_messages;
    }
    public function settle_lang_message($lang = 'english', $shopify_file = 'form_validation_message'){
        $cls_lang_file = '../language/'.$lang.'/'.$file.'.php';
        if(file_exists($cls_lang_file)){
            if(empty(self::$shopify_lang_messages['form_validation'])){
                require_once $cls_lang_file;
                self::$shopify_lang_messages['form_validation'] = $cls_lang_arr;
            }
        }else{
            echo "<pre>";
            print_r("$cls_lang_file file not exist ! lang 404.");
            echo "</pre>";
            exit;
        }
    }
    
}

