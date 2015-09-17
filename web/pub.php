<?php

header('P3P: CP="IDC DSP COR CURa ADMa STP HIS IDVi IVDi IVAi PSD TATi DEVo CAO PSA CONi OUR IND PHY ONL COM STA NOI COR NID CUR ADM DEV CNT BUS DEVi"');
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('pub', 'prod', false);

sfConfig::set('app_user_session_ns', 'pub');
$configuration->shut();

sfContext::createInstance($configuration)->dispatch();
