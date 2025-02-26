<?php
global $global, $config;
if (!isset($global['systemRootPath'])) {
    require_once '../videos/configuration.php';
}
allowOrigin();
AVideoPlugin::loadPlugin('MobileManager');
header('Content-Type: application/json');
$obj = new stdClass();
$obj->max_file_size = get_max_file_size();
$obj->file_upload_max_size = file_upload_max_size();
$obj->videoStorageLimitMinutes = $global['videoStorageLimitMinutes'];
$obj->currentStorageUsage = getSecondsTotalVideosLength();
$obj->webSiteLogo = $config->getLogo(true);
$obj->webSiteTitle = $config->getWebSiteTitle();
$obj->PHPSESSID = session_id();
$obj->version = $config->getVersion();
$obj->mobileSreamerVersion = MobileManager::getVersion();
$obj->reportVideoPluginEnabled = AVideoPlugin::isEnabledByName("ReportVideo");
$obj->oauthLogin = [];
$obj->oauthLogin[] = ['type' => 'Facebook', 'status'=> !empty(AVideoPlugin::loadPluginIfEnabled('LoginFacebook'))];
$obj->oauthLogin[] = ['type' => 'Google', 'status'=> !empty(AVideoPlugin::loadPluginIfEnabled('LoginGoogle'))];
$obj->oauthLogin[] = ['type' => 'Twitter', 'status'=> !empty(AVideoPlugin::loadPluginIfEnabled('LoginTwitter'))];
$obj->oauthLogin[] = ['type' => 'LinkedIn', 'status'=> !empty(AVideoPlugin::loadPluginIfEnabled('LoginLinkedin'))];
$obj->oauthLogin[] = ['type' => 'Apple', 'status'=> !empty(AVideoPlugin::loadPluginIfEnabled('LoginApple'))];

$obj->plugins = [];
$plugins = Plugin::getAllEnabled();
foreach ($plugins as $value) {
    $p = AVideoPlugin::loadPlugin($value['dirName']);
    if (is_object($p)) {
        $info = $p->getMobileInfo();
        if (!empty($info)) {
            $obj->plugins[$value['dirName']] = $info;
        }
    }
}

echo json_encode($obj);
