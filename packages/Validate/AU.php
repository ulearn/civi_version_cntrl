<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2005 Daniel O'Connor                              |
// +----------------------------------------------------------------------+
// | This source file is subject to the New BSD license, That is bundled  |
// | with this package in the file LICENSE, and is available through      |
// | the world-wide-web at                                                |
// | http://www.opensource.org/licenses/bsd-license.php                   |
// | If you did not receive a copy of the new BSDlicense and are unable   |
// | to obtain it through the world-wide-web, please send a note to       |
// | pajoye@php.net so we can mail you a copy immediately.                |
// +----------------------------------------------------------------------+
// | Author: Daniel O'Connor <daniel.oconnor@gmail.com>                   |
// |         Alex Hayes <ahayes@wcg.net.au>                               |
// |         Byron Adams <byron.adams54@gmail.com>                        |
// +----------------------------------------------------------------------+
//
/**
 * Specific validation methods for data used in Australia
 *
 * @category   Validate
 * @package    Validate_AU
 * @author     Daniel O'Connor <daniel.oconnor@gmail.com>
 * @author     Tho Nguyen <tho.nguyen@itexperts.com.au>
 * @author     Alex Hayes <ahayes@wcg.net.au>
 * @author     Byron Adams <byron.adams54@gmail.com>
 * @copyright  1997-2005 Daniel O'Connor
 * @copyright  (c) 2006 Alex Hayes
 * @copyright  (c) 2006 Byron Adams
 * @version    CVS: $Id: AU.php,v 1.12 2006/08/29 03:15:54 ahayes Exp $
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 */

define("VALIDATE_AU_PHONENUMBER_STRICT",        1);
define("VALIDATE_AU_PHONENUMBER_NATIONAL",      2);
define("VALIDATE_AU_PHONENUMBER_INDIAL",        4);
define("VALIDATE_AU_PHONENUMBER_INTERNATIONAL", 8);

/**
 * Data validation class for Australia
 *
 * Contains code from Validate_AT, Validate_UK and Validate_NZ
 *
 * This class provides methods to validate:
 *  - Postal code
 *  - Phone number
 *  - Australian Business Number
 *  - Australian Company Number
 *  - Tax File Number
 *  - Australian Regional codes
 *
 * @category   Validate
 * @package    Validate_AU
 * @author     Daniel O'Connor <daniel.oconnor@gmail.com>
 * @author     Tho Nguyen <tho.nguyen@itexperts.com.au>
 * @author     Alex Hayes <ahayes@wcg.net.au>
 * @author     Byron Adams <byron.adams54@gmail.com>
 * @copyright  1997-2005 Daniel O'Connor
 * @copyright  (c) 2006 Alex Hayes
 * @copyright  (c) 2006 Byron Adams
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 */

class Validate_AU
{

    /**
     * Validate Austrialian postal codes.
     *
     * @access   public
     * @static   string  $postcodes
     * @param    string  $postcode  postcode to validate
     * @param    bool    $strong optional; strong checks against a list of postcodes
     * @return   bool    true if postcode is ok, false otherwise
     */
    function postalCode($postcode, $strong = false)
    {
        if ($strong) {
            static $postcodes;

            if (!isset($postcodes)) {
                $file = '/home/shot/work/CiviCRM/svn/trunk/packages/data/Validate_AU/data/AU_postcodes.txt';
                $postcodes = array_map('trim', file($file));
            }

            return in_array((int)$postcode, $postcodes);
        }
        return preg_match('(^[0-9]{4}$)', $postcode);
    }
    
    /**
     * Validates Australian Regional Codes
     *
     * @author    Byron Adams <byron.adams54@gmail.com>
     * @access    public
     * @static    array      $regions
     * @param     string     $region, regional code to validate
     * @return    bool       Returns true on success, false otherwise
     * @link      http://www.google.com/apis/adwords/developer/adwords_api_regions.html#Australia
     */
    function region($region)
    {
        static $regions = array("ACT", "NSW", "NT", "QLD", "SA", "TAS", "VIC", "WA");
        return in_array(strtoupper($region),$regions);
    }

    /**
     * Validate a telephone number.
     *
     * Note that this function supports the following notations:
     * 
     *     - Landline: 03 9999 9999
     *     - Mobile: 0400 000 000 (as above, but usually notated differently)
     *     - Indial: 131 812 / 1300 000 000 / 1800 000 000 / 1900 000 000
     *     - International: +61.3 9999 9999
     * 
     * For International numbers, only +61 will be valid, as this is
     * Australia's dial code, and the format MUST be +61.3, where 3 represents
     * the state dial code, in this case, Victoria.
     * 
     * Note: If the VALIDATE_AU_PHONENUMBER_STRICT flag is not supplied, then all spaces, 
     * dashes and parenthesis are removed before validation. You will have to 
     * strip these yourself if your data storage does not allow these characters.
     *
     * @static
     * @access    public
     * @param string $number    The telephone number
     * @param int $flags        Can be a combination of the following flags:
     *                              - <b>VALIDATE_AU_PHONENUMBER_STRICT</b>: if
     *                                supplied then no spaces, parenthesis or dashes (-)
     *                                will be removed.
     *                              - <b>VALIDATE_AU_PHONENUMBER_NATIONAL</b>: when supplied
     *                                valid national numbers (eg. 03 9999 9999) will return true.
     *                              - <b>VALIDATE_AU_PHONENUMBER_INDIAL</b>: when supplied
     *                                valid indial numbers (eg. 13/1300/1800/1900) will return true.
     *                              - <b>VALIDATE_AU_PHONENUMBER_INTERNATIONAL</b>: when supplied
     *                                valid international notation of Australian numbers 
     *                                (eg. +61.3 9999 9999) will return true.
     * @return    bool
     * 
     * @author Alex Hayes <ahayes@wcg.net.au>
     * @author Daniel O'Connor <daniel.oconnor@gmail.com>
     * 
     * @todo Check that $flags contains a valid flag.
     */
    function phoneNumber($number, $flags = VALIDATE_AU_PHONENUMBER_NATIONAL)
    {

        if(!($flags & VALIDATE_AU_PHONENUMBER_STRICT)) {
            $number = str_replace(
                array('(', ')', '-', ' '), 
                '', 
                $number
            );
        }

        if($flags & VALIDATE_AU_PHONENUMBER_NATIONAL) {
             $preg[] = "(0[23478][0-9]{8})";
        }

        if($flags & VALIDATE_AU_PHONENUMBER_INDIAL) {
             $preg[] = "(13[0-9]{4}|1[3|8|9]00[0-9]{6})";
        }

        if($flags & VALIDATE_AU_PHONENUMBER_INTERNATIONAL) {
             $preg[] = "(\+61\.[23478][0-9]{8})";
        }

        if(is_array($preg)) {
            $preg = implode("|", $preg);
            return preg_match("/^" . $preg . "$/", $number) ? true : false;
        }

        return false;

    }

   /**
     * Validate an Australian Company Number (ACN)
     * 
     * The ACN is a nine digit number with the last digit
     * being a check digit calculated using a modified 
     * modulus 10 calculation.
     *
     * @access  public
     * @param   string  $acn; ACN number to validate
     * @return  bool    Returns true on success, false otherwise
     * @link    http://www.asic.gov.au/asic/asic_infoco.nsf/byheadline/Australian+Company+Number+(ACN)+Check+Digit
     * @author  Byron Adams <byron.adams54@gmail.com>
     * @author  Daniel O'Connor <daniel.oconnor@gmail.com>
     */
    function acn($acn)
    {
        $weights = array(8, 7, 6, 5, 4, 3, 2, 1);    
    
        $acn = preg_replace("/[^\d]/", "", $acn);
        $digits = str_split($acn);
        $sum = 0;
        
        if (!ctype_digit($acn) || strlen($acn) != 9) {
            return false;
        }
        
        foreach ($digits as $key => $digit) {
            $sum += $digit * $weights[$key];
        }
        
        $remainder = $sum % 10;
        
        switch ($remainder) {
            case 0:
                $complement = 0 - $remainder;
                break;
            default:
                $complement = 10 - $remainder;
                break;
        }
        
        return ($digits[8] == $complement);
    }

    /**
     * Social Security Number.
     *
     * Australia does not have a social security number system,
     * the closest equivalent is a Tax File Number 
     *
     * @access  public
     * @see     Validate_AU::tfn()
     * @param   string  $ssn; ssn number to validate
     * @return  bool    Returns true on success, false otherwise
     */
    function ssn($ssn)
    {
        return Validate_AU::tfn($ssn);
    }

    /**
     * Tax File Number (TFN)
     *
     * Australia does not have a social security number system,
     * the closest equivalent is a Tax File Number.
     *
     * @access  public
     * @param   $tfn    Tax File Number
     * @return  bool    Returns true on success, false otherwise
     * @link    http://en.wikipedia.org/wiki/Tax_File_Number
     * @author  Byron Adams <byron.adams54@gmail.com>
     */
    function tfn($tfn)
    {
        $weights = array(1, 4, 3, 7, 5, 8, 6, 9, 10);
        $length = array("8", "9");
        
        $tfn = preg_replace("/[^\d]/", "", $tfn);
        $tfn = str_split($tfn);
        
        return Validate_AU::checkDigit($tfn, 11, $weights, $length);
    }

    /**
     * Australian Business Number (ABN).
     * 
     * Validates an ABN using a modulus calculation
     *
     * @static
     * @access  public
     * @param   string    $abn    ABN to validate
     * @return  bool      true on success, otherwise false
     * @link    http://www.ato.gov.au/businesses/content.asp?doc=/content/13187.htm
     * @author  Byron Adams <byron.adams54@gmail.com>
     */
    function abn($abn)
    {
        $weights = array(10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19);    
        $length = array("11");
        
        $abn = preg_replace("/[^\d]/", "", $abn);
        $abn = str_split($abn);
        $abn[0]--;
        
        return Validate_AU::checkDigit($abn, 89, $weights, $length);
    }

    /**
     * Validate number against decimal checksum (check digit)  
     * 
     * A check digit is a form of redundancy check used 
     * for error detection, the decimal equivalent of a 
     * binary checksum. It consists of a single digit 
     * computed from the other digits in the message.
     *
     * @access  public
     * @param   array    $digits; Digits to check
     * @param   int      $modulus;
     * @param   array    $weights;
     * @param   array    $length; 
     * @return  bool     true on success, otherwise false
     * @link    http://en.wikipedia.org/wiki/Check_digit
     * @author  Byron Adams <byron.adams54@gmail.com>
     */
    function checkDigit($digits, $modulus, $weights, $length)
    {
        $sum = 0;
    
        if (!in_array(count($digits), $length)) {
            return false;
        }
    
        foreach ($digits as $key => $digit) {
            $sum += $digit * $weights[$key];
        }
    
        return !($sum % $modulus);
    
    }
}
?>
