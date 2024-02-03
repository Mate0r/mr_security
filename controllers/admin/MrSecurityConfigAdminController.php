<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class MrSecurityConfigAdminController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }


    public function postProcess() {

    }


    public function initContent()
    {
        parent::initContent();
        $this->module->assignVarsAdmin();

        $action = isset($_GET['action']) ? "action".ucfirst(strtolower($_GET['action'])) : "actionView";
        if (method_exists($this, $action)) {
            return $this->$action();
        }
        
        return $this->actionView();
    }


    public function actionView()
    {
        try {

            $vulnerabilities = [];
            $error = false;

            if (ini_get('allow_url_fopen')) {
                $vulnerabilities = MrSecurityVulnerability::all();
            } else {
                $error = "Vous devez activer allow_url_fopen dans le fichier php.ini";
            }

        } catch (Exception $exception) {
            $error = $exception->getMessage();
        }

        return $this->module->displayAdmin('vulnerabilities.tpl', array(
            'vulnerabilities' => $vulnerabilities,
            'error' => $error
        ));
    }


    public function actionPatch()
    {
        if (!$vulnerability = MrSecurityVulnerability::find(Tools::getValue('vulnerability_cve_id'))) {
            return $this->module->json(['error' => "La vulnerabilitée est introuvable : CVE ID incorrect"]);
        }

        if (!$vulnerability->patch()) {
            return $this->module->json(['error' => "La vulnérabilitée n'a pas pu être patchée"]);
        }

        return $this->module->json(['success' => "La vulnérabilitée a été patchée"]);
    }


    public function actionDownload()
    {
        if (!$data = file_get_contents("https://modules.mate0r.com/prestashop/mr_security_v_1_0_1.zip")) {
            return $this->module->json(['error' => "Module updated version not found"]);
        }

        if (!file_put_contents(MR_SECURITY_PATH."/download/mr_security_v_1_0_1.zip", $data)) {
            return $this->module->json(['error' => "Module updated version download error"]);
        }

        return $this->module->json(['success' => "Le module a été téléchargé"]);
    }


    public function actionInstall()
    {
        # disable module
        if (!$this->module->disable()) {
            return $this->module->json(['error' => "can't disable module"]);
        }

        # uninstall module
        if (!$this->module->uninstall()) {
            return $this->module->json(['error' => "can't uninstall module"]);
        }

        # open zip archive
        $zip = new ZipArchive();
        if ($zip->open(MR_SECURITY_PATH."/download/mr_security_v_".str_replace(".", "_", $this->module->version_last).".zip") !== true) {
            return $this->module->json(['error' => "can't open zip module update"]);
        }

        # extract files
        $zip->extractTo(_PS_MODULE_DIR_);
        $zip->close();

        # reinstall module
        if (!$this->module->install()) {
            return $this->module->json(['error' => "can't reinstall module"]);
        }

        # re-enable module
        if (!$this->module->enable()) {
            return $this->module->json(['error' => "can't enable module"]);
        }

        return $this->module->json(['redirect' => Context::getContext()->link->getAdminLink("MrSecurityConfigAdmin")]);
    }
}