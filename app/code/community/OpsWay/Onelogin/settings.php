<?php

$metadataUrl =  Mage::getStoreConfig('dev/onelogin/metadata_url');
$assertionUrl = Mage::getStoreConfig('dev/onelogin/assertion_url');
$assertionIndex = Mage::getStoreConfig('dev/onelogin/assertion_index');
$logoutUrl = Mage::getStoreConfig('dev/onelogin/logout_url');

require_once('_toolkit_loader.php');

$settings = array (

    'strict' => false,
    'debug' => false,

    'sp' => array (
        'entityId' => 'php-saml',
        'assertionConsumerService' => array (
            'url' => Mage::helper("adminhtml")->getUrl(),
        ),
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
				'privateKey' => Mage::getStoreConfig('dev/onelogin/sp_private_key'),
        'x509cert' => Mage::getStoreConfig('dev/onelogin/sp_certificate')
    ),
    'idp' => array (
        'entityId' => $metadataUrl,
        'singleSignOnService' => array (
            'url' => $assertionUrl,
        ),
        'singleLogoutService' => array (
            'url' => $assertionIndex,
        ),
        'x509cert' => Mage::getStoreConfig('dev/onelogin/certificate')
    ),

    'security' => array (
        'signMetadata' => false,
        'nameIdEncrypted' => false,
        'authnRequestsSigned' => false,
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,
        'wantMessagesSigned' => false,
        'wantAssertionsSigned' => false,
        'wantAssertionsEncrypted' => false,
    )
);
if (($entityId = Mage::getStoreConfig('dev/onelogin/entity_id')) != '') {
	  $settings['sp']['entityId'] = $entityId;
}
if (Mage::getStoreConfig('dev/onelogin/sign_requests')) {
	  $settings['security']['authnRequestsSigned'] = true;
}