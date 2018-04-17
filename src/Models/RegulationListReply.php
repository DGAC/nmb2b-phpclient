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

namespace DSNA\NMB2BDriver\Models;

/**
 * Class RegulationListReply
 * @package DSNA\NMB2BDriver\Models
 */
class RegulationListReply extends SoapResponse
{
    public function getRegulations() {
        if($this->getXML() != null) {
            return $this->getXML()->xpath('//regulations/item');
        } else {
            return null;
        }
    }

    /**
     * @param $regulation
     * @return string
     */
    public static function getDataId($regulation) {
        return (string) $regulation->regulationId;
    }

    /**
     * @param $regulation
     * @return string
     */
    public static function getRegulationName($regulation) {
        return (string) $regulation->location->id;
    }

    public static function getDescription($regulation) {
        return (string) $regulation->location->description;
    }

    public static function getNormalRate($regulation)
    {
        return (string) $regulation->initialConstraints->normalRate;
    }

    /**
     * @param $regulation
     * @return string
     */
    public static function getReason($regulation) {
        return (string) $regulation->reason;
    }

    /**
     * @param $regulation
     * @return \DateTime
     */
    public static function getDateTimeStart($regulation) {
        $time = $regulation->applicability->wef . '+00:00';
        return new \DateTime($time);
    }

    /**
     * @param $regulation
     * @return \DateTime
     */
    public static function getDateTimeEnd($regulation) {
        $time = $regulation->applicability->unt . '+00:00';
        return new \DateTime($time);
    }

    public static function getRegulationState($regulation)
    {
        return (string) $regulation->regulationState;
    }
}