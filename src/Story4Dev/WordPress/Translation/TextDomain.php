<?php
namespace Story4Dev\WordPress\Translation;

use Story4Dev\Interfaces\HooksInterface;

use Story4Dev\WordPress\Helpers\PostType;

/**
 * TextDomain
 *
 * @author JOELINJATOVO Haja
 * @version 1.0.0
 * @since 1.0.0
 */
class TextDomain implements HooksInterface{

    /**
     * @see Story4Dev\Interfaces\HooksInterface
     */
    public function hooks(){
        $this->load_textdomain();
    }
			
    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        $plugin_rel_path = 'story4dev/languages/';
        load_plugin_textdomain('story4dev', false, $plugin_rel_path );
    }
    
}