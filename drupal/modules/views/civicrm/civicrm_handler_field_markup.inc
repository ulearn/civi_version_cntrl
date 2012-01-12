<<<<<<< HEAD
<?php
/**
 * A handler to run a field through check_markup, using the default format
 *
 */
class civicrm_handler_field_markup extends views_handler_field {
    function construct() {
        parent::construct();
    }

    function render($values) {
        $value = $values->{$this->field_alias};
        $retval = check_markup($value, filter_fallback_format(), FALSE);
        return ( $retval == 'n/a' ) ? null : $retval;
    }
}
=======
<?php
/**
 * A handler to run a field through check_markup, using the default format
 *
 */
class civicrm_handler_field_markup extends views_handler_field {
    function construct() {
        parent::construct();
    }

    function render($values) {
        $value = $values->{$this->field_alias};
        return check_markup($value, FILTER_FORMAT_DEFAULT, FALSE);
    }
}
>>>>>>> 9b665e700dc440d9c08362550834b1dbe1d9b7b2
