<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

// The sole purpose of this class is to fix a six-year-old bug in
// PEAR which makes Mail_mime wrongly encode email-sporting headers

require_once 'packages/Mail/mime.php';

class CRM_Utils_Mail_FixedMailMIME extends Mail_mime
{
    // a wrapper for the original function; this fixes PEAR bug #30 and CRM-4631
    function _encodeHeaders($input, $params = array())
    {
        require_once 'CRM/Utils/Rule.php';
        $emailValues = array( );
        foreach ( $input as $fieldName => $fieldValue ) {
            $fieldNames = $emails = array( );
            $hasValue = false;
            
            //multiple email w/ comma separate. 
            $fieldValues = explode( ',', $fieldValue );
            foreach ( $fieldValues as $index => $value ) {
                $value = trim( $value );
                //might be case we have only email address.
                if ( CRM_Utils_Rule::email( $value ) ) {
                    $hasValue = true;
                    $emails[$index]     = $value;
                    $fieldNames[$index] = 'FIXME_HACK_FOR_NO_NAME';
                } else {
                    $matches = array( );
                    if ( preg_match('/^(.*)<([^<]*)>$/', $value, $matches ) ) {
                        $hasValue = true;
                        $emails[$index]     = $matches[2];
                        $fieldNames[$index] = trim($matches[1]);
                    }
                }
            }
            
            //get formatted values back in input
            if ( $hasValue ) {
                $input[$fieldName]       = implode( ',', $fieldNames );
                $emailValues[$fieldName] = implode( ',', $emails );
            }
        }
        
        // encode the email-less headers
        $input = parent::_encodeHeaders( $input, $params );
        
        // add emails back to headers, quoting these headers along the way
        foreach ( $emailValues as $fieldName => $value ) {
            $emails     = explode( ',', $value );
            $fieldNames = explode( ',', $input[$fieldName] );
            
            foreach ( $fieldNames as $index => &$name ) {
                $name = str_replace( '\\', '\\\\', $name );
                $name = str_replace( '"',  '\"',   $name );
                
                // CRM-5640 -if the name was actually doubly-quoted, 
                // strip these(the next line will add them back);
                if ( substr( $name, 0, 2 ) == '\"' && substr( $name, -2 ) == '\"' ) {
                    $name =  substr( $name, 2, -2 );
                }
            }
            
            //combine fieldNames and emails.
            $mergeValues = array( );
            foreach ( $emails as $index => $email ) {
                if ( $fieldNames[$index] == 'FIXME_HACK_FOR_NO_NAME' ) {
                    $mergeValues[] = $email;   
                } else {
                    $mergeValues[] = "\"$fieldNames[$index]\" <$email>"; 
                }
            }
            
            //finally get values in.
            $input[$fieldName] = implode( ',', $mergeValues );
        }
        
        return $input;
    }
    
}
