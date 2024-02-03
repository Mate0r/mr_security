<?php
/**
* 2007-2024 MaTe0r
*
*  MODULE mr_security
*
*  @author    MaTe0r <mateo.rinaldi01@gmail.com>
*  @copyright MaTe0r <https://portfolio.mate0r.com>
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

define('MR_SECURITY_NAME', "mr_security");
define('MR_SECURITY_PATH', _PS_MODULE_DIR_.MR_SECURITY_NAME);
define('MR_SECURITY_URL', '/modules/'.MR_SECURITY_NAME.'/');

require_once(MR_SECURITY_PATH."/models/MrModule.php");
require_once(MR_SECURITY_PATH."/models/MrSecurityVulnerability.php");


class mr_security extends Module
{
    public $mr_module;
    public $version_last;

    public function __construct()
    {
        $this->name = 'mr_security';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'MaTe0r';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->mr_module = new MrModule($this);

        parent::__construct();

        $this->displayName = $this->l('MrSecurity');
        $this->description = $this->l('Scan and clean your site of vulnerabilities published by Friend of Prestashop (FOP)');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => '8.1.99.99');

        // get last version
        $this->version_last = $this->version;
        if ($json = json_decode(file_get_contents("https://modules.mate0r.com/prestashop/modules.json"))) {
            if (isset($json->{"mr_security"}, $json->{"mr_security"}->last)) {
                if ($json->{"mr_security"}->last !== $this->version) {
                    $this->version_last = $json->{"mr_security"}->last;
                }
            }
        }
    }


    public function __call($name, $arguments)
    {
        if (method_exists($this->mr_module, $name)) {
            return call_user_func_array(array($this->mr_module, $name), $arguments);
        }

        if (method_exists($this, $name)) {
            return call_user_func_array(array($this, $name), $arguments);
        }
    }


    /**
     * Install function called at installation of module
     */
    public function install()
    {
        # install reservation tabs
        if (!$this->tabControllerAdd('Mr Security', 'MrSecurityConfigAdmin', 0)) {
            throw new Exception("La création du tab Config a échouée");
        }

        # install module parent
        if (!parent::install()) {
            throw new Exception("L'installation du module (parent) a échouée");
        }

        # install hooks
        if (!$this->registerHook('actionAdminControllerSetMedia')) {
            throw new Exception("L'installation des hooks a échouée");
        }

        return true;
    }


    /**
     * Uninstall function called at uninstallation of module
     */
    public function uninstall()
    {
        # call uninstall module function (parent)
        if (!parent::uninstall()) {
            throw new Exception("La désinstallation du module (parent) a échouée");
        }

        return true;
    }    
    

    /**
     * Call the configuration controller for the module
     */
    public function getContent()
    {
        return $this->redirect($this->getAdminLink('MrSecurityConfigAdmin'));
    }


    /**
     * Add the CSS & JavaScript files you want to be added on the Back Office
     */
    public function hookActionAdminControllerSetMedia()
    {
        # font awesome
        $this->context->controller->addCSS("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css");

        # toastr
        $this->context->controller->addCSS($this->_path.'lib/toastr/toastr.min.css');
        $this->context->controller->addJS($this->_path.'lib/toastr/toastr.min.js');

        # default files
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
        $this->context->controller->addJS($this->_path.'views/js/back.js');
    }


    public function assignVarsAdmin()
    {
        $this->context->smarty->assign('mr_security_controller', $this->context->controller->controller_name);
        $this->context->smarty->assign('mr_security_link_config', $this->context->link->getAdminLink("MrSecurityConfigAdmin"));
        $this->context->smarty->assign('mr_security_error', $this->getError());
        $this->context->smarty->assign('mr_security_success', $this->getSuccess());
        $this->context->smarty->assign('mr_security_name', $this->name);
        $this->context->smarty->assign('mr_security_version', $this->version);
        $this->context->smarty->assign('mr_security_version_last', $this->version_last);
        $this->context->smarty->assign('mr_security_support_link', "https://portfolio.mate0r.com");
    }
}