<?php



class Model_Constants_Constant
{

    public static $SORT_URL_PARAM_NAME = "sort";
    public static $SORTORDER_URL_PARAM_NAME = "sorder";
    public static $SEARCH_URL_PARAM_NAME = 'search';
    public static $SEARCH_URL_VALUE_NAME = 'Search';
    public static $SEARCH_CRITERIA_PARAM_NAME = 'searchCriteria';


    // Messages
    public static $SUCCESS_SAVE_MESSAGE = "Successfully Saved";
    public static $ERROR_SAVE_MESSAGE = "Error in saving data";

    // Button
    public static $ADD_BUTTON_LABEL = "Add New";

    // Pdf
    public static $DEFAULT_LINE_HEIGHT = "15";
    public static $DEFAULT_INVOICE_FOLDER = "invoices";
    public static $DEFAULT_CHARACTER_WIDTH = "5"; // 5px

    public static $MONTH_TO_DAYS_MAP = array(
        '01' => 31,
        '1' => 31,
        '02' => 30,
        '2' => 30,
        '03' => 31,
        '3' => 31,
        '04' => 30,
        '4' => 30,
        '05' => 31,
        '5' => 31,
        '06' => 30,
        '6' => 30,
        '07' => 31,
        '7' => 31,
        '08' => 31,
        '8' => 31,
        '09' => 30,
        '9' => 30,
        '10' => 31,
        '11' => 30,
        '12' => 31
    );
}

?>