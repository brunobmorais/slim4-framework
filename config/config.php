<?php
if (strpos($_SERVER['SERVER_NAME'],"localhost") || $_SERVER['SERVER_NAME'] == "localhost"){
    require_once(dirname(__DIR__,1).'/config/developerConfig.php');
} else {
    require_once(dirname(__DIR__,1).'/config/productionConfig.php');
}
/*ALTERE ESSA VARIAVEL TODA VEZ QUE QUISER ATUALIZAR O CSS E JAVASCRIPT*/
const CONFIG_VERSION_CODE = "1.0.0";

const CONFIG_MAINTENANCE = false;

const CONFIG_SECURITY = [
    "domain" => 'site.com',
    "token" => 'TOKEN',
    "permission_domains" => ['site.com', 'site.com', 'www.site.com']
];

const CONFIG_SITE = [
    "color-primary" => "#035E96",
    "color-primary-hover" => "#024670",
    "color-secondary" => "#676767",
    "name" => "Nome curto site",
    "nameFull" => "Nome completo site",
    "email" => "meuemail@site.com",
    "phone" => "+55 63 3312-3333",
    "url" => "https://site.com",
    "domain" => "https://site.com",
    "andress" => "Palmas-TO",
    "cnpj" => ""
];

const CONFIG_DEVELOPER = [
    "name" => "Meu site",
    "nameFull" => "Meu site completo",
    "email" => "meusite@gmail.com",
    "url" => "https://www.meusite.com.br"
];

// CONFIGURAÇÃO HEADER
const CONFIG_HEADER = [
    "author" => 'Meu site',
    "title" => 'Meu site',
    "description" => 'Nome completo site',
    "image" => 'https://meusite.com/assets/img/ic_logosocial.jpg',
    "keywords" => "site",
    "color" => CONFIG_SITE['color-primary'],
    "fbAppId" => "0"
];

// CONFIGURAÇÃO EMAIL
const CONFIG_EMAIL = [
    "host" => 'smtp.gmail.com',
    "userName" => "naoresponda@meusite.com",
    "password" => '',
    "port" => '465',
    "smtpAuth" => true,
    "smtpSecure" => 'ssl',
    "from" => "naoresponda@weblighting.com.br",
    "reply" => "naoresponda@weblighting.com.br"
];

// CONFIGURAÇÃO HEADER
// URL: CHAVES https://www.google.com/u/1/recaptcha/admin
// EMAIL: email@gmail.com
const CONFIG_RECAPTCHA = [
    "chaveSite" => '',
    "chaveSecreta" => '',
];
