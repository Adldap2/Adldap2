<?php

/**
 * This file contains a working example of adLDAP in operation.
 *
 * @file
 */
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    echo 'Before using this demo, please run <code>composer dump-autoload</code>';
    exit(1);
}
require_once __DIR__.'/../vendor/autoload.php';

use adLDAP\adLDAP;
use adLDAP\Exceptions\adLDAPException;

// Set up all options.
$options = [
    'account_suffix' => '',
    'base_dn' => null,
    'domain_controllers' => [''],
    'admin_username' => null,
    'admin_password' => null,
    'real_primarygroup' => '',
    'use_ssl' => false,
    'use_tls' => false,
    'recursive_groups' => true,
    'ad_port' => adLDAP::ADLDAP_LDAP_PORT,
    'sso' => '',
];
// Update options from $_POST.
foreach ($options as $optName => $defaultValue) {
    if (isset($_POST[$optName])) {
        $options[$optName] = $_POST[$optName];
    }
}
// Validate options.
$options['domain_controllers'] = array_filter($options['domain_controllers']);

// Try to bind.
$adldap = false;
$exception = false;
if (is_array($options['domain_controllers']) && !empty($options['domain_controllers'][0])) {
    try {
        $adldap = new adLDAP($options);
        // To pass through to the form:
        $options['base_dn'] = $adldap->getBaseDn();
        $options['ad_port'] = $adldap->getPort();
    } catch (adLDAPException $e) {
        $exception = $e;
    }
}

// Handle log in.
$username = (!empty($_POST['username'])) ? $_POST['username'] : '';
$info = false;
if ($adldap && !empty($username)) {
    $password = $_POST['password'];
    try {
        $adldap->authenticate($username, $password);
        $info = $adldap->user()->info($username, ['*']);
        if (isset($info[0])) {
            $info = $info[0];
        }
    } catch (\adLDAP\Exceptions\adLDAPException $e) {
        $exception = $e;
    }
}

// Hand everything over to the view for display.
require 'view.html.php';
