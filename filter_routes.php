
<?php
### This is how i would have done it. Assume you have two database tables:

/***********************************************************
1. table_operator
    -> op_id (Primary Key)
    -> op_name (or smsc router)

2. table_operator_code
    -> op_id (Foreign Key - referenced from table_operator)
    -> code (first 3 digits of a mobile number)
*************************************************************/

    // Returns the first 3 digit Op code from the number
    function get_mobile_Op_code($val) {
        return substr($val, 0, 3);
    }

    // This method assumes that the mobile number passed as a parameter has been checked for validity: 
    // For example === 0712111222 or 712111222 (these are both valid tigo numbers)... 
    // Call this method before you send sms to get the operator channel of which you will send the sms to...
    // Modify this concept to your environment (This method is not very efficient for very large number array in a loop, due to number of database calls)...

    function get_SMSC_from_mobile($mobile) {
        $temp = ltrim($mobile, 0);
        $mobileOpCode = get_mobile_Op_code($temp);

        $query = "SELECT op.op_name FROM table_operator_code opc 
                INNER JOIN table_operator op ON op.op_id = opc.op_id WHERE opc.code = ?";

        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $mobileOpCode); 
        $stmt->execute();
        $stmt->bind_result($op_name);
        $stmt->fetch();
        $stmt->close();

        return $op_name;
    }
?>

