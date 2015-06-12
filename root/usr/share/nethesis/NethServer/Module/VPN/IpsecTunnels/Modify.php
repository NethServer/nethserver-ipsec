<?php
namespace NethServer\Module\VPN\IpsecTunnels;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;

/**
 * Modify IPSEC tunnels
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Modify extends \Nethgui\Controller\Table\Modify
{
    private $ciphers = array('aes128', 'aes192', 'aes256', '3des');
    private $hashes = array('sha1', 'sha2_256', 'sha2_384', 'sha2_512', 'md5');
    private $pfsgroups = array('modp1024','modp1536', 'modp2048', 'modp3072', 'modp4096', 'modp6144', 'modp8192');

    public function initialize()
    {
        $yn = $this->createValidator()->memberOf(array('yes', 'no'));
        $ac = $this->createValidator()->memberOf(array('auto', 'custom'));
        $parameterSchema = array(
            array('name', Validate::USERNAME, \Nethgui\Controller\Table\Modify::KEY),
            array('left', Validate::IPv4, \Nethgui\Controller\Table\Modify::FIELD),
            array('leftsubnets', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('leftid', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('right', Validate::IPv4, \Nethgui\Controller\Table\Modify::FIELD),
            array('rightsubnets', Validate::NOTEMPTY, \Nethgui\Controller\Table\Modify::FIELD),
            array('rightid', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::FIELD),
            array('psk', $this->createValidator()->minLength(20), \Nethgui\Controller\Table\Modify::FIELD),
            array('ikelifetime', Validate::POSITIVE_INTEGER, \Nethgui\Controller\Table\Modify::FIELD),
            array('salifetime', Validate::POSITIVE_INTEGER, \Nethgui\Controller\Table\Modify::FIELD),
            array('ike', $ac, \Nethgui\Controller\Table\Modify::FIELD),
            array('ikecipher', $this->createValidator()->memberOf($this->ciphers), \Nethgui\Controller\Table\Modify::FIELD),
            array('ikehash', $this->createValidator()->memberOf($this->hashes), \Nethgui\Controller\Table\Modify::FIELD),
            array('ikepfsgroup', $this->createValidator()->memberOf($this->pfsgroups), \Nethgui\Controller\Table\Modify::FIELD),
            array('esp', $ac, \Nethgui\Controller\Table\Modify::FIELD),
            array('espcipher', $this->createValidator()->memberOf($this->ciphers), \Nethgui\Controller\Table\Modify::FIELD),
            array('esphash', $this->createValidator()->memberOf($this->hashes), \Nethgui\Controller\Table\Modify::FIELD),
            array('esppfsgroup', $this->createValidator()->memberOf($this->pfsgroups), \Nethgui\Controller\Table\Modify::FIELD),
            array('status', Validate::SERVICESTATUS, \Nethgui\Controller\Table\Modify::FIELD),
            array('pfs', $yn, \Nethgui\Controller\Table\Modify::FIELD),
            array('compress', $yn, \Nethgui\Controller\Table\Modify::FIELD),
            array('dpdaction', $this->createValidator()->memberOf(array('restart','hold')), \Nethgui\Controller\Table\Modify::FIELD),
        );
        

        $this->setSchema($parameterSchema);
        $this->setDefaultValue('status', 'enabled');
        $this->setDefaultValue('ike', 'auto');
        $this->setDefaultValue('esp', 'auto');
        $this->setDefaultValue('compress', 'no');
        $this->setDefaultValue('pfs', 'yes');
        $this->setDefaultValue('dpdaction', 'hold');
        $this->setDefaultValue('ikelifetime', '86400');
        $this->setDefaultValue('salifetime', '3600');
        $this->setDefaultValue('leftsubnets', implode(",",$this->readNetworks()));

        parent::initialize();
    }

   public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $templates = array(
            'create' => 'NethServer\Template\VPN\IpsecTunnels\Modify',
            'update' => 'NethServer\Template\VPN\IpsecTunnels\Modify',
            'delete' => 'Nethgui\Template\Table\Delete',
        );
        $view->setTemplate($templates[$this->getIdentifier()]);


        $view['ikecipherDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->ciphers);
        $view['ikehashDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->hashes);
        $view['ikepfsgroupDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->pfsgroups);

        $view['espcipherDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->ciphers);
        $view['esphashDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->hashes);
        $view['esppfsgroupDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->pfsgroups);

        $left = array();
        foreach ($this->readIPs() as $ipaddr => $props) {
            $left[] = array($ipaddr, $ipaddr ." - ".$props[0]." (".$props[1].")");
        }
        $view['leftDatasource'] = $left;

    }

    private function maskToCidr($mask){
        $long = ip2long($mask);
        $base = ip2long('255.255.255.255');
        return 32-log(($long ^ $base)+1,2);
    }

    private function readIPs()
    {
        $ret = array();
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $interface => $props) {
            if(isset($props['role']) && isset($props['ipaddr'])) {
                $ret[$props['ipaddr']] = array($interface,$props['role']);
            }
        }
        return $ret;
    }

    private function readNetworks()
    {
        $ret = array();
        $interfaces = $this->getPlatform()->getDatabase('networks')->getAll();
        foreach ($interfaces as $interface => $props) {
            if(isset($props['role']) && isset($props['ipaddr']) && $props['role'] == 'green') {
                $net = long2ip(ip2long($props['ipaddr']) & ip2long($props['netmask']));
                $cidr = $this->maskToCidr($props['netmask']); 
                $ret[] = "$net/$cidr";
            }
        }
        return $ret;
    }


    protected function onParametersSaved($changedParameters)
    {
        $event = $this->getIdentifier();
        if ($event == "update") {
            $event = "modify";
        }
#        $this->getPlatform()->signalEvent(sprintf('nethserver-vpn-%s@post-process', $event), array($this->parameters['name']));
    }

}
