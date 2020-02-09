<?php

namespace ImportWP\Common\Properties;

use ImportWP\Common\Util\Singleton;

class Properties
{
    use Singleton;

    public $plugin_dir_path;
    public $plugin_folder_name;
    public $plugin_basename;
    public $view_dir;
    public $plugin_domain;
    public $plugin_version;
    public $plugin_file_path;

    public $rest_version;
    public $rest_namespace;

    public function __construct()
    {
        $this->plugin_file_path = realpath(dirname(__DIR__) . '/../../importwp.php');
        $this->plugin_dir_path = plugin_dir_path($this->plugin_file_path);
        $this->plugin_folder_name = basename($this->plugin_dir_path);
        $this->plugin_basename = plugin_basename($this->plugin_file_path);
        $this->plugin_domain = 'importwp';
        $this->plugin_version = IWP_VERSION;
        $this->is_pro = false;

        $this->view_dir = $this->plugin_dir_path . trailingslashit('views');

        $this->rest_namespace = 'iwp';
        $this->rest_version = 'v1';
    }
}
