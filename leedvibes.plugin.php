<?php
/*
@name LeedVibes
@author Simounet <contact@simounet.net>
@link http://www.simounet.net
@licence AGPL-3.0
@version 1.5.1
@description A Leed interface close to the old good Netvibes
*/

include( __DIR__ . '/classes/Utils.php' );

function leedvibes( &$event ) {
    $utils = new Utils();
    $pubdate = $event->getPubdate();
    $date = $utils->formatDate( $pubdate );
    $event->dateLessThanOneHour = $date && isset( $date['less-than-one-hour'] ) ? true : false;
    $event->dateValue = $date ? $date['value'] : '';
    $event->datetime = $date ? $date['datetime'] : '';

    $feed = new Feed();
    $usedFeed = $feed->getById($event->getFeed());
    $event->feedName = $usedFeed->getName();
}

function setLeedvibesTemplate() {
    $pathParts = array_reverse( explode( '/', dirname(__FILE__) ) );
    raintpl::configure("tpl_dir", $pathParts[1] . '/' . $pathParts[0] . '/' );
}

function setLeedvibesNewEventsFilter( &$_, &$filter, &$article_conf ) {
    if( isset( $_['custom-action'] )
        && $_['custom-action'] == 'new-events'
    ) {
        if( isset( $_['last-id-checked'] ) && $_['last-id-checked'] !== 'undefined' ) {
            $filter[MYSQL_PREFIX . 'event`.`id'] = '> ' . $_['last-id-checked'];
        }
        $filter['unread'] = '1';
        $article_conf['articlePerPages'] = 100;
    }
}

Plugin::addHook("event_pre_title", "leedvibes");
Plugin::addHook("index_pre_treatment", "setLeedvibesTemplate");
Plugin::addHook("article_pre_action", "setLeedvibesNewEventsFilter");

?>
