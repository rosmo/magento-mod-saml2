<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Openid observer model
 *
 */
class OpsWay_Onelogin_Model_Observer
{
    public function coreBlockAbstractToHtmlAfter($event)
    {
        /* @var $block Mage_Core_Block_Abstract */
        $block = $event->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Template && 'login.phtml' === $block->getTemplate()) {
            $html = $event->getTransport()->getHtml();
            
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $xml = simplexml_import_dom($dom);
            /* @var $formButtons SimpleXMLElement */
            $formButtons = current($xml->xpath('//form[@id=\'loginForm\']//div[@class=\'form-buttons\']'));
            
            $oneloginLink = $formButtons->addChild('a', Mage::helper('opsway_onelogin')->__('Login via SSO'));
            $oneloginLink->addAttribute('class', 'left');
            $oneloginLink->addAttribute('style', 'margin-left: 10px');

            require(dirname(dirname(__FILE__)).'/settings.php');
            $SAMLsettings = new OneLogin_Saml2_Settings($settings);
            $idpData = $SAMLsettings->getIdPData();
            $idpSSO = '';
            if (isset($idpData['singleSignOnService']) && isset($idpData['singleSignOnService']['url'])) {
							  if (Mage::app()->getRequest()->getParam('saml2_login') === '1')
								{
									$idpSSO = $idpData['singleSignOnService']['url'];
									$auth = new Onelogin_Saml2_Auth($SAMLsettings);
									$signXml = Mage::getStoreConfig('dev/onelogin/sign_xml');
									$event->getTransport()->setHtml($auth->login(null, $signXml));
									return;
								} else {
									$idpSSO = '?saml2_login=1';
								}
            }
            
            $oneloginLink->addAttribute('href', $idpSSO);

            $html = $xml->saveXML();
            
            $event->getTransport()->setHtml($html);
        }
    }
}
