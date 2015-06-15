<?php
namespace NethServer\Module\VPN;

/*
 * Copyright (C) 2013 Nethesis S.r.l.
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
 * Common parameters for xl2tpd and IPsec daemons
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Ipsec extends \Nethgui\Controller\AbstractController
{
    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('status', Validate::SERVICESTATUS, array(array('configuration', 'ipsec', 'ServerStatus'), array('configuration', 'xl2tpd', 'status')));
        $this->declareParameter('KeyType', $this->createValidator()->memberOf(array('rsa', 'psk')), array('configuration', 'ipsec', 'KeyType'));
        $this->declareParameter('KeyPskSecret', Validate::NOTEMPTY, array('configuration', 'ipsec', 'KeyPskSecret'));
        $this->declareParameter('L2tpNetwork', Validate::IPv4, array('configuration', 'ipsec', 'L2tpNetwork'));
        $this->declareParameter('L2tpNetmask', Validate::IPv4_NETMASK, array('configuration', 'ipsec', 'L2tpNetmask'));
    }

    public function readStatus($ipsec, $xl2tpd)
    {
        return $ipsec;
    }

    public function writeStatus($status) {
        return array($status, $status);
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('nethserver-ipsec-save');
    }
}
