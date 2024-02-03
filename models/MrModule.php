<?php
/**
*  @author    MaTe0r <contact@prestashop.myycms.com>
*  @copyright MaTe0r
*  @license   MaTe0r All right reserved
*/

if (defined('_PS_VERSION_') && !class_exists('MrModule')) {

    class MrModule
    {
        public $module;

        public function __construct($module)
        {
            $this->module = $module;
        }


        /**
         * Get current context
         */
        public function getContext()
        {
            return Context::getContext();
        }


        /**
         * Dump some vars
         */
        public function dump($data)
        {
            echo "<pre>";
            var_dump($data);
            echo "</pre>";
        }


        /**
         * Add errors to display
         */
        public function addError(string $message)
        {
            $error = isset($errors[$message][$this->getContext()->language->iso_code])
            ? $errors[$message][$this->getContext()->language->iso_code] : $message;
            $this->getContext()->cookie->__set("mr_security_error", $error);
        }


        /**
         * Add success to display
         */
        public function addSuccess(string $message)
        {
            $succes = isset($success[$message][$this->getContext()->language->iso_code])
            ? $success[$message][$this->getContext()->language->iso_code] : $message;
            $this->getContext()->cookie->__set("mr_security_success", $succes);
        }


        /**
         * Check if we are in ajax request
         */
        public function isAjax()
        {
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
        }


        /**
         * Redirect to a link
         */
        public function redirect(string $link)
        {
            return Tools::redirect($link);
        }


        /**
         * Redirect to a link with setting error
         */
        public function redirectError(string $message, string $link)
        {
            if ($this->isAjax()) {
                return $this->json(array(
                    'error' => $this->l($message),
                    'redirect' => $link
                ));
            }

            $this->addError($message);
            return $this->redirect($link);
        }


        /**
         * Redirect to a link with setting success
         */
        public function redirectSuccess(string $message, string $link)
        {
            if ($this->isAjax()) {
                return $this->json(array(
                    'success' => $this->l($message),
                    'redirect' => $link
                ));
            }

            $this->addSuccess($message);
            return $this->redirect($link);
        }


        /**
         * Return json
         */
        public function json($data = array())
        {
            die(json_encode($data));
        }


        /**
         * Generate admin link of a controller
         */
        public function getAdminLink(string $controller)
        {
            return $this->getContext()->link->getAdminLink($controller);
        }


        /**
         * Get list of errors
         */
        public function getError()
        {
            $error = $this->getContext()->cookie->__get('mr_security_error');
            $this->getContext()->cookie->__unset('mr_security_error');
            return $error;
        }


        /**
         * Get list of success
         */
        public function getSuccess()
        {
            $success = $this->getContext()->cookie->__get('mr_security_success');
            $this->getContext()->cookie->__unset('mr_security_success');
            return $success;
        }


        /**
         * Insert a new tab in prestahop back office menu
         */
        public function tabAdd(string $name, string $class_name, int $id_parent = 0, string $icon = '')
        {
            $tab = new Tab();
            $tab->name[$this->getContext()->language->id] = $this->module->l($name);
            $tab->class_name = $class_name;
            $tab->module = $this->module->name;
            $tab->id_parent = $id_parent;
            $tab->icon = $icon;
            return $tab->add() ? $tab : false;
        }


        /**
         * Insert a new tab in prestahop back office menu
         */
        public function tabControllerAdd(string $name, string $class_name, int $id_parent = 0, string $icon = '')
        {
            $tab = new Tab();
            $tab->name[$this->getContext()->language->id] = $this->module->l($name);
            $tab->class_name = $class_name;
            $tab->module = $this->module->name;
            $tab->id_parent = $id_parent;
            $tab->icon = $icon;
            $tab->active = 0;
            return $tab->add() ? $tab : false;
        }


        /**
         * Get an existing tab by class name
         */
        public function tabGetByClassname(string $classname)
        {
            if (!$id_tab = (int)Tab::getIdFromClassName($classname)) {
                return false;
            }

            $tab = new Tab($id_tab);
            return $tab->id ? $tab : false;
        }


        /**
         * Delete tab's associated to module
         */
        public function tabsDelete()
        {
            foreach (Tab::getCollectionFromModule($this->module->name) as $tab) {
                if (!$tab->delete()) {
                    return false;
                }
            }

            return true;
        }


        /**
         * Install SQL of module
         */
        public function installSQL()
        {
            if (!is_file(dirname(dirname(__FILE__)).'/sql/install.php')) {
                return false;
            }

            require(dirname(dirname(__FILE__)).'/sql/install.php');
            return true;
        }


        /**
         * Uninstall SQL of module
         */
        public function uninstallSQL()
        {
            if (!is_file(dirname(dirname(__FILE__)).'/sql/uninstall.php')) {
                return false;
            }

            require(dirname(dirname(__FILE__)).'/sql/uninstall.php');
            return true;
        }
        

        /**
         * Insert a new log in prestahop
         * 1 = information
         * 2 = warning
         * 3 = error
         * 4 = fatal error
         */
        public function logAdd(string $message, int $severity = 1)
        {
            return PrestaShopLogger::addLog("(".$this->module->name.") ".$message, $severity);
        }


        /**
         * Log error by exception object
         */
        public function logError(Throwable $exception)
        {
            $message = $exception->getMessage()." in ".$exception->getFile()."(".$exception->getLine().")";
            return $this->logAdd($message, 3);
        }


        /**
         * Get an enabled module by name
         */
        public static function getModuleEnabledByName(string $module_name)
        {
            if (!Module::isEnabled($module_name)) {
                return false;
            }

            return Module::getInstanceByName($module_name);
        }


        /**
         * Get a front controller by name (with prefix in option)
         */
        public function getFrontController(string $name)
        {
            # create variables
            $controller_name = $name."Controller";
            $controller_path = $this->module->getLocalPath()."controllers/front/".$controller_name.".php";
            
            # check file
            if (!is_file($controller_path)) {
                return false;
            }

            # require file and return isntance of it
            require_once($controller_path);
            if (!$controller = new $controller_name()) {
                return false;
            }

            $controller->module = $this->module;
            return $controller;
        }


        /**
         * Get an admin controller by name (with prefix in option)
         */
        public function getAdminController(string $name)
        {
            # create variables
            $controller_name = $name."Controller";
            $controller_path = $this->module->getLocalPath()."/controllers/admin/".$controller_name.".php";
            
            # check file
            if (!is_file($controller_path)) {
                return false;
            }

            # require file and return isntance of it
            require_once($controller_path);
            if (!$controller = new $controller_name()) {
                return false;
            }

            $controller->module = $this->module;
            return $controller;
        }
        

        /**
         * Display a tpl in admin
         */
        public function displayAdmin(string $tpl, array $variables = array())
        {
            if (!$this->getContext()->controller->viewAccess()) {
                return;
            }

            $this->getContext()->smarty->assign($variables);
            $content = $this->module->fetch($this->module->getLocalPath().'views/templates/admin/'.$tpl);
            $this->getContext()->smarty->assign('content', $content);
            return $content;
        }


        /**
         * Display a tpl in front
         */
        public function displayFront(string $tpl, array $variables = array())
        {
            $this->getContext()->smarty->assign($variables);
            return $this->module->display($this->module->getLocalPath(), 'views/templates/front/'.$tpl);
        }


        /**
         * Return a language
         */
        public function getLang(int $id_lang)
        {
            $language = Language::getLanguage($id_lang);
            return $language ? (object)$language : false;
        }
    }
}
