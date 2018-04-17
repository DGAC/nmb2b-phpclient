<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

namespace DSNA\NMB2BDriver\Services;

use DSNA\NMB2BDriver\Models\RegulationListReply;

/**
 * Class AirspaceServices
 * @package DSNA\NMB2BDriver\Services
 */
class FlowServices extends Service
{
    public function queryRegulations(\DateTime $start, \DateTime $end, $regex = "LF*", array $requestedRegulationFields = null)
    {
        $now = new \DateTime('now');

        if(strlen($regex) == 0) $regex = "LF*";

        $params = array(
            'sendTime' => $now->format('Y-m-d H:i:s'),
            'queryPeriod' => array(
                'wef' => $start->format('Y-m-d H:i'),
                'unt' => $end->format('Y-m-d H:i')
            ),
            'dataset' => array(
                'type' => 'OPERATIONAL'
            ),
            'tvs' => array(
                'item' => explode(",",$regex)
            )
        );

        if($requestedRegulationFields == null) {
            $params['requestedRegulationFields'] = array(
                'item' => array('location','reason','lastUpdate', 'applicability', 'initialConstraints', 'regulationState')
            );
        } else {
            $params['requestedRegulationFields'] = array(
                'item' => $requestedRegulationFields
            );
        }

        $this->getSoapClient()->queryRegulations($params);

        return new RegulationListReply($this->getSoapClient()->__getLastResponse());
    }
}