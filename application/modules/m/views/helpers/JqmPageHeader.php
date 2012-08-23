<?php

/**
 * a page view helper to help display the header for jQuery Mobile Pages
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class M_View_Helper_JqmPageHeader extends Zend_View_Helper_Abstract
{
    public static function jqmPageHeader() {
        $header = '<div><a href="/m" data-role="button" data-icon="home" rel="external">Home</a></div>'
			. '<div><h1><img src="/images/m/connectphilly_house.jpg" width="450" alt="Connect Philly"></h1></div>';
        
$header = '<div>'
            . '<a href="/m" data-role="button" data-icon="home" rel="external"></a>'
        . '</div>';
			
        return $header;
    }
}
?>
