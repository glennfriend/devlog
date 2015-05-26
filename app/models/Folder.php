<?php

/**
 *  Folder
 *
 */
class Folder extends BaseObject
{

    /**
     *  請依照 table 正確填寫該 field 內容
     *  @return array()
     */
    public static function getTableDefinition()
    {
        return array(
            'key' => array(
                'type'    => 'string',
                'filters' => array('strip_tags','trim'),
                'storage' => 'getKey',
                'field'   => 'key',
            ),
            'real' => array(
                'type'    => 'string',
                'filters' => array('strip_tags','trim'),
                'storage' => 'getReal',
                'field'   => 'real',
            ),
            'name' => array(
                'type'    => 'string',
                'filters' => array('strip_tags','trim'),
                'storage' => 'getName',
                'field'   => 'name',
            ),
            'mtime' => array(
                'type'    => 'timestamp',
                'filters' => array('dateval'),
                'storage' => 'getMtime',
                'field'   => 'mtime',
                'value'   => 0,
            ),
            'properties' => array(
                'type'    => 'string',
                'filters' => array('arrayval'),
                'storage' => 'getProperties',
                'field'   => 'properties',
            ),
        );
    }

    /**
     *  validate
     *  @return messages array()
     */
    public function validate()
    {
        return array();
    }

    /* ------------------------------------------------------------------------------------------------------------------------
        basic method rewrite or extends
    ------------------------------------------------------------------------------------------------------------------------ */

    /**
     *  Disabled methods
     *  @return array()
     */
    public static function getDisabledMethods()
    {
        return array();
    }

    /* ------------------------------------------------------------------------------------------------------------------------
        extends
    ------------------------------------------------------------------------------------------------------------------------ */



    /* ------------------------------------------------------------------------------------------------------------------------
        lazy loading methods
    ------------------------------------------------------------------------------------------------------------------------ */

}

