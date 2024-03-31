<?php
/**
 * Provides Base Model Class
 */

namespace BitApps\BTCBI\Model;

/**
 * Undocumented class
 */
// use BitApps\BTCBI\Model\Model;

use BitApps\BTCBI\Config;
use BitApps\BTCBI\Model\Model;
use BTCBI\Deps\BitApps\WPDatabase\Model as WPDatabaseModel;

/**
 * @property int id
 * @property string name
 * @property string triggered_entity
 * @property string triggered_entity_id
 * @property object flow_details
 * @property boolean status
 * @property int user_id
 * @property string user_ip
 * @property string created_at
 * @property string updated_at
 */
class FlowModel extends WPDatabaseModel
{
    protected $table = 'flow';

    protected $prefix = Config::VAR_PREFIX;
}
