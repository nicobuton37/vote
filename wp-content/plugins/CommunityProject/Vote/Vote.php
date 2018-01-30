<?php 

namespace CommunityProject\Vote ;

require_once __DIR__. '/Settings.php';
class Vote {

    private $settings;

    public function __construct()
    {
        $this->settings = new Settings();
    }
}

