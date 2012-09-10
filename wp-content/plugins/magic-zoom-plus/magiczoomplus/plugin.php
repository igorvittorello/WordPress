<?php
/*

Copyright 2012 MagicToolbox (email : support@magictoolbox.com)

*/

$error_message = false;

function WordPress_MagicZoomPlus_activate () {

    if(!function_exists('file_put_contents')) {
        function file_put_contents($filename, $data) {
            $fp = fopen($filename, 'w+');
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }


    //fix url's in css files
    $fileContents = file_get_contents(dirname(__FILE__) . '/core/magiczoomplus.css');
    $cssPath = preg_replace('/https?:\/\/[^\/]*/is', '', get_option("siteurl"));

    $cssPath .= '/wp-content/'.preg_replace('/^.*?\/(plugins\/.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__))).'/core';

    $pattern = '/url\(\s*(?:\'|")?(?!'.preg_quote($cssPath, '/').')\/?([^\)\s]+?)(?:\'|")?\s*\)/is';
    $replace = 'url(' . $cssPath . '/$1)';
    $fixedFileContents = preg_replace($pattern, $replace, $fileContents);
    if($fixedFileContents != $fileContents) {
        file_put_contents(dirname(__FILE__) . '/core/magiczoomplus.css', $fixedFileContents);
    }

    magictoolbox_WordPress_MagicZoomPlus_init() ;
}

function WordPress_MagicZoomPlus_deactivate () {
    delete_option("WordPressMagicZoomPlusCoreSettings");
}

function showMessage($message, $errormsg = false) {
    if ($errormsg) {
        echo '<div id="message" class="error">';
    } else {
        echo '<div id="message" class="updated fade">';
    }
    echo "<p><strong>$message</strong></p></div>";
}    


function showAdminMessages(){
    global $error_message;
    if (current_user_can('manage_options')) {
       showMessage($error_message,true);
    }
}

function  magictoolbox_WordPress_MagicZoomPlus_init() {

    global $error_message;

    /* add filters and actions into WordPress */
    add_action("admin_menu", "magictoolbox_WordPress_MagicZoomPlus_config_page_menu");

    //add_action("template_redirect", "magictoolbox_WordPress_MagicZoomPlus_styles"); //load scripts and styles only for frontend
	add_action("wp_head", "magictoolbox_WordPress_MagicZoomPlus_styles"); //load scripts and styles


    add_filter("the_content", "magictoolbox_WordPress_MagicZoomPlus_create", 13); //filter content





    if (!file_exists(dirname(__FILE__) . '/core/magiczoomplus.js')) {
        $jsContents = file_get_contents('http://www.magictoolbox.com/static/magiczoomplus/trial/magiczoomplus.js');
        if (!empty($jsContents) && preg_match('/\/\*.*?\\\*/is',$jsContents)){
            if ( !is_writable(dirname(__FILE__) . '/core/')) {
                $error_message = 'The '.substr(dirname(__FILE__),strpos(dirname(__FILE__),'wp-content')).'/core/magiczoomplus.js file is missing. Please re-uplaod it.';
            }
            file_put_contents(dirname(__FILE__) . '/core/magiczoomplus.js', $jsContents);
            chmod(dirname(__FILE__) . '/core/magiczoomplus.js', 0777);
        } else {
            $error_message = 'The '.substr(dirname(__FILE__),strpos(dirname(__FILE__),'wp-content')).'/core/magiczoomplus.js file is missing. Please re-uplaod it.';
        }
    }

    if ($error_message) add_action('admin_notices', 'showAdminMessages');

    //add_filter("shopp_catalog", "magictoolbox_create", 1); //filter content for SHOPP plugin

/*    require_once(dirname(__FILE__) . '/core/magiczoomplus.module.core.class.php');
    $coreClassName = "MagicZoomPlusModuleCoreClass";
    $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'] = new $coreClassName;
    $coreClass = $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
    $coreClass->params->clear();
    $coreClass->params->appendArray(array("zoom-width"=>array("id"=>"zoom-width","group"=>"Positioning and Geometry","order"=>"140","default"=>"300","label"=>"Zoomed area width (in pixels)","type"=>"num","scope"=>"tool"),"zoom-height"=>array("id"=>"zoom-height","group"=>"Positioning and Geometry","order"=>"150","default"=>"300","label"=>"Zoomed area height (in pixels)","type"=>"num","scope"=>"tool"),"zoom-position"=>array("id"=>"zoom-position","group"=>"Positioning and Geometry","order"=>"160","default"=>"right","label"=>"Zoomed area position","type"=>"array","subType"=>"select","values"=>array("top","right","bottom","left","inner"),"scope"=>"tool"),"zoom-align"=>array("id"=>"zoom-align","group"=>"Positioning and Geometry","order"=>"161","default"=>"top","label"=>"How to align zoom window to an image","type"=>"array","subType"=>"select","values"=>array("right","left","top","bottom","center"),"scope"=>"tool"),"zoom-distance"=>array("id"=>"zoom-distance","group"=>"Positioning and Geometry","order"=>"170","default"=>"15","label"=>"Distance between small image and zoom window (in pixels)","type"=>"num","scope"=>"tool"),"expand-size"=>array("id"=>"expand-size","group"=>"Positioning and Geometry","order"=>"210","default"=>"fit-screen","label"=>"Size of the expanded image","type"=>"text","description"=>"The value can be 'fit-screen', 'original' or width/height. E.g. 'width=400' or 'height=350'","scope"=>"tool"),"expand-position"=>array("id"=>"expand-position","group"=>"Positioning and Geometry","order"=>"220","default"=>"center","label"=>"Precise position of enlarged image (px)","type"=>"text","description"=>"The value can be 'center' or coordinates. E.g. 'top=0, left=0' or 'bottom=100, left=100'","scope"=>"tool"),"expand-align"=>array("id"=>"expand-align","group"=>"Positioning and Geometry","order"=>"230","default"=>"screen","label"=>"Align expanded image relative to screen or thumbnail","type"=>"array","subType"=>"select","values"=>array("screen","image"),"scope"=>"tool"),"expand-effect"=>array("id"=>"expand-effect","group"=>"Effects","order"=>"10","default"=>"back","label"=>"Effect while expanding image","type"=>"array","subType"=>"select","values"=>array("linear","cubic","back","elastic","bounce"),"scope"=>"tool"),"restore-effect"=>array("id"=>"restore-effect","group"=>"Effects","order"=>"20","default"=>"linear","label"=>"Effect while restoring image","type"=>"array","subType"=>"select","values"=>array("linear","cubic","back","elastic","bounce"),"scope"=>"tool"),"expand-speed"=>array("id"=>"expand-speed","group"=>"Effects","order"=>"30","default"=>"500","label"=>"Expand duration (milliseconds: 0-10000)","type"=>"num","scope"=>"tool"),"restore-speed"=>array("id"=>"restore-speed","group"=>"Effects","order"=>"40","default"=>"-1","label"=>"Restore duration (milliseconds: 0-10000, -1: use expand duration value)","type"=>"num","scope"=>"tool"),"expand-trigger"=>array("id"=>"expand-trigger","group"=>"Effects","order"=>"50","default"=>"click","label"=>"Trigger for the enlarge effect","type"=>"array","subType"=>"select","values"=>array("click","mouseover"),"scope"=>"tool"),"expand-trigger-delay"=>array("id"=>"expand-trigger-delay","group"=>"Effects","order"=>"60","default"=>"200","label"=>"Delay before mouseover triggers expand effect (milliseconds: 0 or larger)","type"=>"num","scope"=>"tool"),"restore-trigger"=>array("id"=>"restore-trigger","group"=>"Effects","order"=>"70","default"=>"auto","label"=>"Trigger to restore image to its small state","type"=>"array","subType"=>"select","values"=>array("auto","click","mouseout"),"scope"=>"tool"),"keep-thumbnail"=>array("id"=>"keep-thumbnail","group"=>"Effects","order"=>"80","default"=>"Yes","label"=>"Show/hide thumbnail when image enlarged","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"opacity"=>array("id"=>"opacity","group"=>"Effects","order"=>"270","default"=>"50","label"=>"Square opacity","type"=>"num","scope"=>"tool"),"opacity-reverse"=>array("id"=>"opacity-reverse","group"=>"Effects","order"=>"280","default"=>"No","label"=>"Add opacity to background instead of hovered area","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"zoom-fade"=>array("id"=>"zoom-fade","group"=>"Effects","order"=>"290","default"=>"Yes","label"=>"Zoom window fade effect","type"=>"array","subType"=>"select","values"=>array("Yes","No"),"scope"=>"tool"),"zoom-window-effect"=>array("id"=>"zoom-window-effect","group"=>"Effects","order"=>"291","default"=>"shadow","label"=>"Apply shadow or glow on a zoom window","type"=>"array","subType"=>"select","values"=>array("shadow","glow","false"),"scope"=>"tool"),"zoom-fade-in-speed"=>array("id"=>"zoom-fade-in-speed","group"=>"Effects","order"=>"300","default"=>"200","label"=>"Zoom window fade-in speed (in milliseconds)","type"=>"num","scope"=>"tool"),"zoom-fade-out-speed"=>array("id"=>"zoom-fade-out-speed","group"=>"Effects","order"=>"310","default"=>"200","label"=>"Zoom window fade-out speed  (in milliseconds)","type"=>"num","scope"=>"tool"),"fps"=>array("id"=>"fps","group"=>"Effects","order"=>"320","default"=>"25","label"=>"Frames per second for zoom effect","type"=>"num","scope"=>"tool"),"smoothing"=>array("id"=>"smoothing","group"=>"Effects","order"=>"330","default"=>"Yes","label"=>"Enable smooth zoom movement","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"smoothing-speed"=>array("id"=>"smoothing-speed","group"=>"Effects","order"=>"340","default"=>"40","label"=>"Speed of smoothing (1-99)","type"=>"num","scope"=>"tool"),"pan-zoom"=>array("id"=>"pan-zoom","group"=>"Effects","order"=>"341","default"=>"Yes","label"=>"Zoom/pan the expanded image","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"selectors-change"=>array("id"=>"selectors-change","group"=>"Multiple images","order"=>"110","default"=>"click","label"=>"Method to switch between multiple images","type"=>"array","subType"=>"select","values"=>array("click","mouseover"),"scope"=>"tool"),"selectors-class"=>array("id"=>"selectors-class","group"=>"Multiple images","order"=>"111","default"=>"","label"=>"Define a CSS class of the active selector","type"=>"text","scope"=>"tool"),"preload-selectors-small"=>array("id"=>"preload-selectors-small","group"=>"Multiple images","order"=>"120","default"=>"Yes","label"=>"Multiple images, preload small images","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"preload-selectors-big"=>array("id"=>"preload-selectors-big","group"=>"Multiple images","order"=>"130","default"=>"No","label"=>"Multiple images, preload large images","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"selectors-effect"=>array("id"=>"selectors-effect","group"=>"Multiple images","order"=>"140","default"=>"dissolve","label"=>"Dissolve or cross fade thumbnail when switching thumbnails","type"=>"array","subType"=>"select","values"=>array("dissolve","fade","pounce","disable"),"scope"=>"tool"),"selectors-effect-speed"=>array("id"=>"selectors-effect-speed","group"=>"Multiple images","order"=>"150","default"=>"400","label"=>"Selectors effect speed, ms","type"=>"num","scope"=>"tool"),"selectors-mouseover-delay"=>array("id"=>"selectors-mouseover-delay","group"=>"Multiple images","order"=>"160","default"=>"60","label"=>"Multiple images delay in ms before switching thumbnails","type"=>"num","scope"=>"tool"),"initialize-on"=>array("id"=>"initialize-on","group"=>"Initialization","order"=>"70","default"=>"load","label"=>"How to initialize Magic Zoom and download large image","type"=>"array","subType"=>"radio","values"=>array("load","click","mouseover"),"scope"=>"tool"),"click-to-activate"=>array("id"=>"click-to-activate","group"=>"Initialization","order"=>"80","default"=>"No","label"=>"Click to show the zoom","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"click-to-deactivate"=>array("id"=>"click-to-deactivate","group"=>"Initialization","order"=>"81","default"=>"No","label"=>"Allow click to hide the zoom window","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"show-loading"=>array("id"=>"show-loading","group"=>"Initialization","order"=>"90","default"=>"Yes","label"=>"Loading message","type"=>"array","subType"=>"select","values"=>array("Yes","No"),"scope"=>"tool"),"loading-msg"=>array("id"=>"loading-msg","group"=>"Initialization","order"=>"100","default"=>"Loading zoom...","label"=>"Loading message text","type"=>"text","scope"=>"tool"),"loading-opacity"=>array("id"=>"loading-opacity","group"=>"Initialization","order"=>"110","default"=>"75","label"=>"Loading message opacity (0-100)","type"=>"num","scope"=>"tool"),"loading-position-x"=>array("id"=>"loading-position-x","group"=>"Initialization","order"=>"120","default"=>"-1","label"=>"Loading message X-axis position, -1 is center","type"=>"num","scope"=>"tool"),"loading-position-y"=>array("id"=>"loading-position-y","group"=>"Initialization","order"=>"130","default"=>"-1","label"=>"Loading message Y-axis position, -1 is center","type"=>"num","scope"=>"tool"),"entire-image"=>array("id"=>"entire-image","group"=>"Initialization","order"=>"140","default"=>"No","label"=>"Show entire large image on hover","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"show-title"=>array("id"=>"show-title","group"=>"Title and Caption","order"=>"10","default"=>"top","label"=>"Show the title of the image in the zoom window","type"=>"array","subType"=>"select","values"=>array("top","bottom","disable"),"scope"=>"tool"),"show-caption"=>array("id"=>"show-caption","group"=>"Title and Caption","order"=>"20","default"=>"Yes","label"=>"Show caption","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"caption-source"=>array("id"=>"caption-source","group"=>"Title and Caption","order"=>"30","default"=>"Title","label"=>"Caption source","type"=>"array","subType"=>"select","values"=>array("Title","Description","Both")),"caption-width"=>array("id"=>"caption-width","group"=>"Title and Caption","order"=>"40","default"=>"300","label"=>"Max width of bottom caption (pixels: 0 or larger)","type"=>"num","scope"=>"tool"),"caption-height"=>array("id"=>"caption-height","group"=>"Title and Caption","order"=>"50","default"=>"300","label"=>"Max height of bottom caption (pixels: 0 or larger)","type"=>"num","scope"=>"tool"),"caption-position"=>array("id"=>"caption-position","group"=>"Title and Caption","order"=>"60","default"=>"bottom","label"=>"Where to position the caption","type"=>"array","subType"=>"select","values"=>array("bottom","right","left"),"scope"=>"tool"),"caption-speed"=>array("id"=>"caption-speed","group"=>"Title and Caption","order"=>"70","default"=>"250","label"=>"Speed of the caption slide effect (milliseconds: 0 or larger)","type"=>"num","scope"=>"tool"),"enabled-effect"=>array("id"=>"enabled-effect","group"=>"Miscellaneous","order"=>"10","default"=>"Zoom & Expand","label"=>"Which effect to use","type"=>"array","subType"=>"select","values"=>array("Zoom & Expand","Zoom","Expand","Swap images only")),"class"=>array("id"=>"class","group"=>"Miscellaneous","order"=>"20","default"=>"MagicZoomPlus","label"=>"Class Name","type"=>"array","subType"=>"select","values"=>array("all","MagicZoomPlus")),"show-message"=>array("id"=>"show-message","group"=>"Miscellaneous","order"=>"370","default"=>"Yes","label"=>"Show message under image?","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"message"=>array("id"=>"message","group"=>"Miscellaneous","order"=>"380","default"=>"Move your mouse over image or click to enlarge","label"=>"Message under images","type"=>"text"),"right-click"=>array("id"=>"right-click","group"=>"Miscellaneous","order"=>"385","default"=>"No","label"=>"Show right-click menu on the image","type"=>"array","subType"=>"radio","values"=>array("Yes","Original","Expanded","No"),"scope"=>"tool"),"background-opacity"=>array("id"=>"background-opacity","group"=>"Background","order"=>"10","default"=>"30","label"=>"Opacity of the background effect (0-100)","type"=>"num","scope"=>"tool"),"background-color"=>array("id"=>"background-color","group"=>"Background","order"=>"20","default"=>"#000000","label"=>"Fade background color (RGB)","type"=>"text","scope"=>"tool"),"background-speed"=>array("id"=>"background-speed","group"=>"Background","order"=>"30","default"=>"200","label"=>"Speed of the fade effect (milliseconds: 0 or larger)","type"=>"num","scope"=>"tool"),"buttons"=>array("id"=>"buttons","group"=>"Buttons","order"=>"10","default"=>"show","label"=>"Whether to show navigation buttons","type"=>"array","subType"=>"select","values"=>array("show","hide","autohide"),"scope"=>"tool"),"buttons-display"=>array("id"=>"buttons-display","group"=>"Buttons","order"=>"20","default"=>"previous, next, close","label"=>"Display button","type"=>"text","description"=>"Show all three buttons or just one or two. E.g. 'previous, next' or 'close, next'","scope"=>"tool"),"buttons-position"=>array("id"=>"buttons-position","group"=>"Buttons","order"=>"30","default"=>"auto","label"=>"Location of navigation buttons","type"=>"array","subType"=>"select","values"=>array("auto","top left","top right","bottom left","bottom right"),"scope"=>"tool"),"always-show-zoom"=>array("id"=>"always-show-zoom","group"=>"Zoom mode","order"=>"10","default"=>"No","label"=>"Always show zoom?","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"drag-mode"=>array("id"=>"drag-mode","group"=>"Zoom mode","order"=>"20","default"=>"No","label"=>"Use drag mode?","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"move-on-click"=>array("id"=>"move-on-click","group"=>"Zoom mode","order"=>"30","default"=>"Yes","label"=>"Click alone will also move zoom (drag mode only)","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"x"=>array("id"=>"x","group"=>"Zoom mode","order"=>"40","default"=>"-1","label"=>"Initial zoom X-axis position for drag mode, -1 is center","type"=>"num","scope"=>"tool"),"y"=>array("id"=>"y","group"=>"Zoom mode","order"=>"50","default"=>"-1","label"=>"Initial zoom Y-axis position for drag mode, -1 is center","type"=>"num","scope"=>"tool"),"preserve-position"=>array("id"=>"preserve-position","group"=>"Zoom mode","order"=>"60","default"=>"No","label"=>"Position of zoom can be remembered for multiple images and drag mode","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"fit-zoom-window"=>array("id"=>"fit-zoom-window","group"=>"Zoom mode","order"=>"70","default"=>"Yes","label"=>"Resize zoom window if big image is smaller than zoom window","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"slideshow-effect"=>array("id"=>"slideshow-effect","group"=>"Expand mode","order"=>"10","default"=>"dissolve","label"=>"Visual effect for switching images","type"=>"array","subType"=>"select","values"=>array("dissolve","fade","expand"),"scope"=>"tool"),"slideshow-loop"=>array("id"=>"slideshow-loop","group"=>"Expand mode","order"=>"20","default"=>"Yes","label"=>"Restart slideshow after last image","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"slideshow-speed"=>array("id"=>"slideshow-speed","group"=>"Expand mode","order"=>"30","default"=>"800","label"=>"Speed of slideshow effect (milliseconds: 0 or larger)","type"=>"num","scope"=>"tool"),"z-index"=>array("id"=>"z-index","group"=>"Expand mode","order"=>"40","default"=>"10001","label"=>"The z-index for the enlarged image","type"=>"num","scope"=>"tool"),"keyboard"=>array("id"=>"keyboard","group"=>"Expand mode","order"=>"50","default"=>"Yes","label"=>"Ability to use keyboard shortcuts","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"keyboard-ctrl"=>array("id"=>"keyboard-ctrl","group"=>"Expand mode","order"=>"60","default"=>"No","label"=>"Require Ctrl key to permit shortcuts","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"hint"=>array("id"=>"hint","group"=>"Hint","order"=>"10","default"=>"Yes","label"=>"Display a hint to suggest that image is zoomable","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"hint-text"=>array("id"=>"hint-text","group"=>"Hint","order"=>"15","default"=>"Zoom","label"=>"Show text in the hint","type"=>"text","scope"=>"tool"),"hint-position"=>array("id"=>"hint-position","group"=>"Hint","order"=>"20","default"=>"top left","label"=>"Position of the hint","type"=>"array","subType"=>"select","values"=>array("top left","top right","top center","bottom left","bottom right","bottom center"),"scope"=>"tool"),"hint-opacity"=>array("id"=>"hint-opacity","group"=>"Hint","order"=>"25","default"=>"75","label"=>"Opacity of the hint (0-100)","type"=>"num","scope"=>"tool")));
*/

    if(!isset($GLOBALS['magictoolbox']['WordPressMagicZoomPlus'])) {
        require_once(dirname(__FILE__) . '/core/magiczoomplus.module.core.class.php');
        $coreClassName = "MagicZoomPlusModuleCoreClass";
        $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'] = new $coreClassName;
        $coreClass = &$GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
    }
    $coreClass = &$GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
    /* get current settings */
    $settings = get_option("WordPressMagicZoomPlusCoreSettings");
    if($settings !== false && is_array($settings)) {
        $coreClass->params->appendArray($settings);
    } else {
        update_option("WordPressMagicZoomPlusCoreSettings", $coreClass->params->getArray());
    }

    //add_option("magictoolboxURL", get_option("siteurl")."/wp-content/plugins/magictoolbox/magiczoomplus/core");
}

function WordPressMagicZoomPlus_config_page() {
     magictoolbox_WordPress_MagicZoomPlus_config_page('WordPressMagicZoomPlus');
}

function magictoolbox_WordPress_MagicZoomPlus_config_page_menu() {
    if(function_exists("add_submenu_page")) {
        //$coreClass = & $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];
        $page = add_submenu_page("plugins.php", __("Magic Zoom Plus Plugin Configuration"), __("Magic Zoom Plus Configuration"), "manage_options", "WordPressMagicZoomPlus-config-page", "WordPressMagicZoomPlus_config_page");
		//add_action('admin_print_styles-'.$page,'magictoolbox_WordPress_MagicZoomPlus_admin_styles');
    }
}

function  magictoolbox_WordPress_MagicZoomPlus_config_page($id) {
    $settings = $GLOBALS['magictoolbox'][$id]->params->getArray();
    if(isset($_POST["submit"])) {
        /* save settings */
        foreach($settings as $name => $s) {
            if(isset($_POST["magiczoomplussettings".ucwords(strtolower($name))])) {
                $v = $_POST["magiczoomplussettings".ucwords(strtolower($name))];
                switch($s["type"]) {
                    case "num": $v = intval($v); break;
                    case "array": 
                        $v = trim($v);
                        if(!in_array($v,$s["values"])) $v = $s["default"];
                        break;
                    case "text":
                    default: $v = trim($v);
                }
                $s["value"] = $v;
                $settings[$name] = $s;                
            }
        }
        update_option($id . "CoreSettings", $settings);
        $GLOBALS['magictoolbox'][$id]->params->appendArray($settings);
    }
    
    $toolAbr = '';
    $abr = explode(" ", strtolower("Magic Zoom Plus"));
    foreach ($abr as $word) $toolAbr .= $word{0};
    
     $corePath = preg_replace('/https?:\/\/[^\/]*/is', '', get_option("siteurl"));
     $corePath .= '/wp-content/'.preg_replace('/^.*?\/(plugins\/.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__))).'/core';
    ?>
	<style>
        .<?php echo $toolAbr; ?>params { margin:20px 0; width:90%; border:1px solid #dfdfdf; }
        .<?php echo $toolAbr; ?>params .params { margin:0; width:100%;}
        .<?php echo $toolAbr; ?>params .params th { <? /*white-space:nowrap; */ ?> vertical-align:middle; border-bottom:1px solid #dfdfdf; padding:15px 5px; font-weight:bold; background:#fff; text-align:left; padding:0 20px; }
        .<?php echo $toolAbr; ?>params .params td { vertical-align:middle; border-bottom:1px solid #dfdfdf; padding:10px 5px; background:#fff; width:100%; }
        .<?php echo $toolAbr; ?>params .params tr.back th, .<?php echo $toolAbr; ?>params .params tr.back td { background:#f9f9f9; }
        .<?php echo $toolAbr; ?>params .params tr.last th, .<?php echo $toolAbr; ?>params .params tr.last td { border:none; }
        .afterText {font-size:10px;font-style:normal;font-weight:normal;}
        .settingsTitle {font-size: 1.5em;font-weight: normal;margin: 1.7em 0 1em 0;}
        input[type="checkbox"],input[type="radio"] {margin:5px;vertical-align:middle !important;}
        td img {vertical-align:middle !important; margin-right:10px;}
        td span {vertical-align:middle !important; margin-right:10px;}
		#footer {position:relative;}
    </style>
    
    <div class="icon32" id="icon-options-general"><br></div>
    <h2>Magic Zoom Plus Plugin Settings</h2><br/>
    <p>Learn more about the <a href="http://www.magictoolbox.com/magiczoomplus_integration/" target="_blank">customisation options</a></p>
    <form action="" method="post" id="magiczoomplus-config-form">
            <?php
                $groups = array();
                $imgArray = array('zoom & expand','zoom&expand','yes','zoom','expand','swap images only','no','left','top left','top','top right', 'right', 'bottom right', 'bottom', 'bottom left'); //array for the images ordering

                foreach($settings as $name => $s) { 

                    if (strtolower($s['id']) == 'disable-expand' || strtolower($s['id']) == 'disable-zoom') continue;

                    if (!isset($groups[$s['group']])) {
                        $groups[$s['group']] = array();
                    }

                    $s['value'] = $GLOBALS['magictoolbox'][$id]->params->getValue($name);

                    if (strpos($s["label"],'(')) {
                        $before = substr($s["label"],0,strpos($s["label"],'('));
                        $after = ' '.str_replace(')','',substr($s["label"],strpos($s["label"],'(')+1));
                    } else {
                        $before = $s["label"];
                        $after = '';
                    }
                    if (strpos($after,'%')) $after = ' %';
                    if (strpos($after,'in pixels')) $after = ' pixels';
                    if (strpos($after,'milliseconds')) $after = ' milliseconds';

                    $html  .= '<tr>';
                    $html  .= '<th width="50%">';
                    $html  .= '<label for="magiczoomplussettings'. ucwords(strtolower($name)).'">'.$before.'</label>';

                    if(($s['type'] != 'array') && isset($s['values'])) $html .= '<br/> <span class="afterText">' . implode(', ',$s['values']).'</span>';

                    $html .= '</th>';
                    $html .= '<td width="50%">';

                    switch($s["type"]) {
                        case "array": 
                                $rButtons = array();
                                foreach($s["values"] as $p) {
                                    $rButtons[strtolower($p)] = '<label><input type="radio" value="'.$p.'"'. ($s["value"]==$p?"checked=\"checked\"":"").' name="magiczoomplussettings'.ucwords(strtolower($name)).'" id="magiczoomplussettings'. ucwords(strtolower($name)).$p.'">';
                                    $pName = ucwords($p);
                                    if(strtolower($p) == "yes")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/yes.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "no")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/no.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom-left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom-right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top-left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top-right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    else $rButtons[strtolower($p)] .= '<span>'.ucwords($p).'</span></label>';
                                }
                                foreach ($imgArray as $img){
                                    if (isset($rButtons[$img])) {
                                        $html .= $rButtons[$img];
                                        unset($rButtons[$img]);
                                    }
                                }
                                $html .= implode('',$rButtons);
                            break;
                        case "num": 
                        case "text": 
                        default:
                            if (strtolower($name) == 'message') { $width = 'style="width:95%;"';} else {$width = '';}
                            $html .= '<input '.$width.' type="text" name="magiczoomplussettings'.ucwords(strtolower($name)).'" id="magiczoomplussettings'. ucwords(strtolower($name)).'" value="'.$s["value"].'" />';
                            break;
                    }
                    $html .= '<span class="afterText">'.$after.'</span>';
                    $html .= '</td>';
                    $html .= '</tr>';
                    $groups[$s['group']][] = $html;
                    $html = '';
                }
            foreach ($groups as $name => $group) {
                $i = 0;
                $group[count($group)-1] = str_replace('<tr','<tr class="last"',$group[count($group)-1]); //set "last" class

                echo '<h3 class="settingsTitle">'.$name.'</h3>
                            <div class="'.$toolAbr.'params">
                            <table class="params" cellspacing="0">';

                foreach ($group as $g) {
                    if (++$i%2==0) { //set stripes
                        if (strpos($g,'class="last"')) {
                            $g = str_replace('class="last"','class="back last"',$g);
                        } else {
                            $g = str_replace('<tr','<tr class="back"',$g);
                        }
                    }
                    echo $g;
                }
                echo '</table> </div>';
            }
            ?>
            
            <p><input type="submit" name="submit" class="button-primary" value="Save settings" /></p>
        </form>

   
    </div>
    <div style="font-size:12px;margin:5px auto;text-align:center;">Learn more about the <a href="http://www.magictoolbox.com/magiczoomplus_integration/" target="_blank">customisation options</a></div>
    <?php
}

/*function  magictoolbox_WordPress_MagicZoomPlus_admin_styles() {   

	$path = preg_replace('/^.*?\/plugins\/(.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__)));
	wp_enqueue_style( 'magiczoomplus-admin' , WP_PLUGIN_URL . "/{$path}/core/admin.css");

}

function  magictoolbox_WordPress_MagicZoomPlus_styles() {

	$path = preg_replace('/^.*?\/plugins\/(.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__)));
	wp_enqueue_style( 'magiczoomplus' , WP_PLUGIN_URL . "/{$path}/core/magiczoomplus.css");
	wp_enqueue_script('magiczoomplus', WP_PLUGIN_URL."/{$path}/core/magiczoomplus.js");

}*/

function  magictoolbox_WordPress_MagicZoomPlus_styles() {
    if(!defined('MAGICTOOLBOX_MAGICZOOMPLUS_HEADERS_LOADED')) {
		if (function_exists('plugins_url')) {
			$core_url = plugins_url();
		} else {
			$core_url = get_option("siteurl").'/wp-content/plugins';
		}


        $path = preg_replace('/^.*?\/plugins\/(.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__)));
        echo $GLOBALS['magictoolbox']['WordPressMagicZoomPlus']->headers($core_url."/{$path}/core");

        define('MAGICTOOLBOX_MAGICZOOMPLUS_HEADERS_LOADED', true);
    }
}




function  magictoolbox_WordPress_MagicZoomPlus_create($content) {

    $pattern = "<img([^>]*)(?:>)(?:[^<]*<\/img>)?";
    $pattern = "(?:<a([^>]*)>){$pattern}(.*?)(?:<\/a>)";


    $content = preg_replace_callback("/{$pattern}/is","magictoolbox_WordPress_MagicZoomPlus_callback",$content);


    return $content;
}

function  magictoolbox_WordPress_MagicZoomPlus_callback($matches) {
    $plugin = $GLOBALS['magictoolbox']['WordPressMagicZoomPlus'];


    if(!$plugin->params->checkValue('class', 'all')) {
        if(!preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*" . preg_quote(strtolower($plugin->params->getValue('class')), '/') . "(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0])) {
            return $matches[0];
        }
    } else {
        if (preg_match("/class\s*=\s*[\'\"]\s*(?:[^\"\'\s]*\s)*Magic[A-Za-z]+?(?:\s[^\"\'\s]*)*\s*[\'\"]/iUs",$matches[0])) { //if already modified
            return $matches[0];
        }
    }




    //to put options in rel
    $plugin->general->params['disable-expand'] = $plugin->params->params['disable-expand'];
    $plugin->general->params['disable-zoom'] = $plugin->params->params['disable-zoom'];

    $plugin->params->set('disable-expand','Yes');
    $plugin->params->set('disable-zoom','Yes');
    if($plugin->params->checkValue('enabled-effect', 'Zoom')) {
        $plugin->params->set('disable-expand', 'Yes');
        $plugin->params->set('disable-zoom', 'No');
    } elseif($plugin->params->checkValue('enabled-effect', 'Expand')) {
        $plugin->params->set('disable-zoom', 'Yes');
        $plugin->params->set('disable-expand', 'No');
    } elseif($plugin->params->checkValue('enabled-effect', 'Swap images only')) {
        $plugin->params->set('disable-expand','Yes');
        $plugin->params->set('disable-zoom','Yes');
    } elseif($plugin->params->checkValue('enabled-effect', 'Zoom & Expand')) {
        $plugin->params->set('disable-zoom', 'No');
        $plugin->params->set('disable-expand', 'No');
    }




    
    $alignclass = preg_replace('/^.*?align(left|right|center|none).*$/is', '$1', $matches[2]);
    if($alignclass != $matches[2]) {
        $alignclass = ' align'.$alignclass;
    } else {
        $alignclass='';
        $float = preg_replace('/^.*?float:\s*(left|right|none).*$/is', '$1', $matches[2]);
        if($float == $matches[2]) {
            $float = '';
        } else {
            $float = ' float: ' . $float . ';';
        }
    }
    
/* get needed attributes */
    global $wp_query;
    $alt = preg_replace("/^.*?alt\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[2]);
    $img = preg_replace("/^.*?href\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[1]);
    $thumb = preg_replace("/^.*?src\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[2]);
    $title = preg_replace("/^.*?title\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[0]);
    if($title == $matches[0]) unset($title);
    $id = preg_replace("/^.*?id\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[1]);
    if($id == $matches[1]) unset($id);

    

    $aStyles = $matches[1];
    $imgStyles = $matches[2];
    /* remove id,rel,class,href,title,rev attributes from link */
	$matches[1] = preg_replace("/^(.*?)id\s*=\s*[\"\'].*?[\"\']/is","$1",$matches[1]);
    $matches[1] = preg_replace("/^(.*?)class\s*=\s*[\"\'].*?[\"\']/is","$1",$matches[1]);
    $matches[1] = preg_replace("/^(.*?)title\s*=\s*[\"\'].*?[\"\']/is","$1",$matches[1]);
    $matches[1] = preg_replace("/^(.*?)rev\s*=\s*[\"\'].*?[\"\']/is","$1",$matches[1]);
    $matches[1] = preg_replace("/^(.*?)href\s*=\s*[\"\'].*?[\"\']/is","$1",$matches[1]);
    /* remove src attribute from img */
    $matches[2] = preg_replace("/^(.*?)src\s*=\s*[\"\'].*?[\"\']/is","$1",$matches[2]);
    $matches[2] = preg_replace("/\/\s*$/is"," ",$matches[2]);

    $description = $alt;
    $result = $plugin->template(compact('img','thumb','id','title','description'));

    //restore after the rel was generated
    $plugin->params->params['disable-expand'] = $plugin->general->params['disable-expand'];
    $plugin->params->params['disable-zoom'] = $plugin->general->params['disable-zoom'];


	$result = preg_replace('/id=\"MagicZoomPlus[^\"]*?'. $id.'\"/is', 'id="'.$id.'"', $result);

    $divWidth = @getimagesize($thumb);
    if ($divWidth && is_array($divWidth)) {
        $divWidth = $divWidth[0];
    } else {
        $divWidth = ''; 
    }

    $result = preg_replace("/^(.*?)<a(.*?)$/is","$1<a {$matches[1]}$2",$result);
    $result = preg_replace("/^(.*?)<img(.*?)$/is","$1<img {$matches[2]}$2",$result);
    
    $result = "<div style=\"width:{$divWidth}px;{$float}\" class=\"MagicToolboxContainer {$alignclass}\">{$result}</div>";

    return $result;
    //return $matches[0];
}







?>
