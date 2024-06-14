<?php

function generatePassword($length = 8)
{

    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
        $length = $maxlength;
    }

    // set up a counter for how many characters are in the password so far
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

        // have we already used this character in $password?
        if (!strstr($password, $char)) {
            // no, so it's OK to add it onto the end of whatever we've already got...
            $password .= $char;
            // ... and increase the counter by one
            $i++;
        }
    }

    // done!
    return $password;
}

function escape($string)
{
    return htmlentities($string);
}

function english_date($date)
{
    $create_date = date_create($date);
    $new_date = date_format($create_date, "j M Y");
    return $new_date;
}

function redirect($message, $url)
{
?>
    <script type="text/javascript">
        //        function Redirect()
        //        {
        //            window.location = "<?php echo $url; ?>";
        //        }
        //        alert('<?php echo $message; ?>');
        //        setTimeout('Redirect()', 10);
        alert('<?php echo $message; ?>');
        window.location = "<?php echo $url; ?>"
    </script>
<?php
}

function english_date_time($date)
{
    $create_date = date_create($date);
    $new_date = date_format($create_date, "jS F Y l H:i:s a");
    return $new_date;
}
// Access the base currency variable from the configurations file to pick the currency set by the user in the setting file.
function ugandan_shillings($value)
{
    global $base_currency;
    $value = number_format($value, 0, ".", ",");
    return $value . " ".$base_currency;
}

function addMonthsToDate($months, $dateCovert)
{
    $date = date_create($dateCovert);
    date_add($date, date_interval_create_from_date_string($months . ' months'));
    return date_format($date, 'Y-m-d');
}

function calculateAge($smallDate, $largeDate)
{
    $age = "";
    $diff = date_diff(date_create($smallDate), date_create($largeDate));
    $age .= ($diff->y > 0) ? $diff->y : 0;

    return $age;
}

function calculateDateDifference($smallDate, $largeDate, $type)
{
    $age = 0;
    $diff = strtotime($largeDate) - strtotime($smallDate);
    $age = ($type == "years") ? $diff / (60 * 60 * 24 * 30 * 12) : $age;
    $age = ($type == "months") ? $diff / (60 * 60 * 24 * 30) : $age;
    $age = ($type == "days") ? $diff / (60 * 60 * 24) : $age;
    return number_format($age);
}

//Function to append zero leading to left
function stringPadLeft($string_value)
{
    $value = str_pad($string_value, 2, '0', STR_PAD_LEFT);
    return $value;
}

function getConfigValue($name)
{ //return config value from database
    return DB::getInstance()->getName("config", $name, "value", "name");
}
//function to send SMS
function sendMessage($recipient, $message, $msg_from)
{

    $provider = getConfigValue("sms_provider");
    // Specify your authentication credentials
    $username = getConfigValue("sms_username");
    $apikey = getConfigValue("sms_api_key");
    if ($provider == "AfricasTalking") {
        $sent_status = "";
        // Specify the numbers that you want to send to in a comma-separated list
        // Please ensure you include the country code (+256 for Uganda in this case)
        $recipients = $recipient;

        // And of course we want our recipients to know what we really do
        //$message = "Hello " . $recipients . ", you have just received this msg from Street parking";
        // Create a new instance of our awesome gateway class
        $gateway = new AfricasTalkingGateway($username, $apikey);


        /*         * ***********************************************************************************
          NOTE: If connecting to the sandbox:

          1. Use "sandbox" as the username
          2. Use the apiKey generated from your sandbox application
          https://account.africastalking.com/apps/sandbox/settings/key
          3. Add the "sandbox" flag to the constructor

          $gateway  = new AfricasTalkingGateway($username, $apiKey, "sandbox");
         * ************************************************************************************ */

        // Any gateway error will be captured by our custom Exception class below, 
        // so wrap the call in a try-catch block

        try {
            // Thats it, hit send and we'll take care of the rest. 
            $results = $gateway->sendMessage($recipients, $message);
            foreach ($results as $result) {
                // status is either "Success" or "error message"
                //            echo " Number: " . $result->number;
                //            echo " Status: " . $result->status;
                //            echo " MessageId: " . $result->messageId;
                //            echo " Cost: " . $result->cost . "\n";
                $sent_status = $result->status;
            }
        } catch (AfricasTalkingGatewayException $e) {
            //echo "Encountered an error while sending: " . $e->getMessage();
            $sent_status = "Encountered an error while sending: " . $e->getMessage();
        }

        // DONE!!! 
    } else if ($provider == "egoSMS") {
        $sender = getConfigValue("sms_from");
        $password = getConfigValue("sms_password");
        $url = "www.egosms.co/api/v1/plain/?";
        $recipients = explode(",", $recipient);
        foreach ($recipients as $recipient) {
            $params = "number=[number]&message=[message]&username=[username]&password=[password]&sender=[sender]";

            $search = array("[message]", "[sender]", "[number]", "[username]", "[password]");
            $replace = array(urlencode($message), urlencode($sender), urlencode($recipient), urlencode($username), urlencode($password));
            $parameters = str_replace($search, $replace, $params);
            $live_url = "http://" . $url . $parameters;
            $parse_url = file($live_url);
            $response = $parse_url[0];

            $sent_status = ($response == "OK") ? "Success" : $response;
        }
    } else if ($provider == "pandora") {
        $sender = getConfigValue("sms_from");
        $password = getConfigValue("sms_password");
        $recipients = explode(",", $recipient);
        $sender = $msg_from; //(not more than 20 characters i.e letters and digits)

        $url = "sms.thepandoranetworks.com/API/send_sms/?";
        $message_type = "info";
        $message_category = "bulk";

        foreach ($recipients as $recipient) {

            $parameters = "number=[number]&message=[message]&username=[username]&password=[password]&sender=[sender]&message_type=[message_type]&message_category=[message_category]";

            $parameters = str_replace("[message]", urlencode($message), $parameters);

            $parameters = str_replace("[sender]", urlencode($sender), $parameters);

            $parameters = str_replace("[number]", urlencode($recipient), $parameters);

            $parameters = str_replace("[username]", urlencode($username), $parameters);

            $parameters = str_replace("[password]", urlencode($password), $parameters);

            $parameters = str_replace("[message_type]", urlencode($message_type), $parameters);

            $parameters = str_replace("[message_category]", urlencode($message_category), $parameters);

            $live_url = "https://" . $url . $parameters;
            $parse_url = file($live_url);

            $response = json_decode($parse_url[0]);

            $sent_status = ($response->success == true) ? "Success" : $response;

            //return json_decode($response, true);
        }
    }
    return $sent_status;
}

class CustomArrayFilter {
    private $num;

    function __construct($num) {
            $this->num = $num;
    }
    function isSameSubject($item){
        return $item->Subject_Id==$this->num;
    }
    function isSamePaper($item){
        return $item->Paper_Id==$this->num;
    }

    function isInRange($g) {
        return $g->Initial<=$this->num&&$g->Final>=$this->num;
            // return $i < $this->num;
    }
    function isInGradeRange($g) {
        return $g->Initial_Marks<=$this->num&&$g->Final_Marks>=$this->num;
            // return $i < $this->num;
    }
    function isSameStudent($item){
        return $item->Student_Id==$this->num;
    }
}
function uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
        // 48 bits for "node"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
function isAuthorized($action)
{
    global $user_permissions;
    global $crypt;

    if (!in_array($action, $user_permissions)) {
        $_SESSION["message"] = array('status' => "danger", 'message' => "Access permission not granted", 'counts' => 0);
        Redirect::to('index.php?page=' . $crypt->encode('dashboard'));
        exit;
    }
}
function generate_thermal_receipt($code, $token)
{
    global $paymentType, $date_today;
    $balance = 0;
    $discount = 0;
    if ($paymentType == "transaction_payment") {
        $transactions = DB::getInstance()->querySample("SELECT *,a.Name AS Source,(CASE WHEN Target_Table='student' THEN (SELECT CONCAT(Fname,' ',Lname) FROM student WHERE Student_Id=t.Target_Id) WHEN Target_Table='staff' THEN (SELECT CONCAT(Fname,' ',Lname) FROM staff WHERE Staff_Id=t.Target_Id) ELSE t.Target_Id END) AS Payee FROM transaction t,account a WHERE (t.Debit_Account=a.Id OR t.Credit_Account=a.Id) AND a.Status=1 AND t.Status=1 AND t.Transaction_Code='$code' GROUP BY t.Transaction_Code ORDER BY t.Id DESC");
        $transaction = $transactions[0];
    }
    if ($transaction) {
        $amount_paid=array_sum(array_column(json_decode(json_encode($transactions), true), 'Amount'));
        $paymentReason="";
        $receiptHeader = COMPANY_LOGO?'<p class="centered"><img src="logo/'.COMPANY_LOGO.'"/>':'';
        $receiptHeader .= COMPANY_ADDRESS ? '<br/><span class="address">' . COMPANY_ADDRESS . '</span>' : '';
        $receiptHeader .= COMPANY_DESCRIPTION ? '<br/><span class="address">' . COMPANY_DESCRIPTION . '</span>' : '';
        $receiptHeader .= COMPANY_PHONE ? '<br/>' . COMPANY_PHONE : '';
        $receiptHeader .= '<br/><u>RECEIPT</u></p>';
        $receiptHeader .= "<table><tr><td>No: <u>$transaction->Transaction_Code</u></td><td>Date: <u>" . english_date($transaction->Date) . "</u></td><td>$transaction->Term</td></tr><tr><td>By</td><td>$transaction->Payee</td><td>Payment Mode</td><td>$transaction->Transaction_Mode</td><td>Ref. Number</td><td>$transaction->Reference_Number</td></tr></table>";
        
        $receiptContent = "<span class='text-justify'>Received with thanks from: $transaction->Payee<br/>
            Amount in words: " . NumberToWord::getInstance()->toText($amount_paid) . " shillings only.<br/>
            Being paid of: $paymentReason <br/><b>Receipt Details</b><table>";
            $receiptContent.="<tr><th>Account</th><th>Qty</th><th>Amount (Unit price)</th><th>Amount(Total)</th><th>Description</th></tr>";
            foreach ($transactions AS $transaction){
                $receiptContent.="<tr><td>$transaction->Source</td>
                <td>$transaction->Quantity</td>
                <td>$transaction->Unit_Price</td>
                <td>$transaction->Amount</td>
                <td>$transaction->Comment</td></tr>";
            }

        // if ($transaction->discount) {
        //     $receiptContent .= "<tr><td>Discount</td><td>" . ugandan_shillings($transaction->discount)."</td></tr>";
        // }
        // $receiptContent .= "<tr><td>Amount Paid</td><td>" . ugandan_shillings($amount_paid)."</td></tr>";
        // if ($balance || $paymentType == "ticket_payment") {
        //     $receiptContent .= "<tr><td>Balance</td><td>" . ugandan_shillings($balance)."</td></tr>";
        // }
        $receiptContent .= "</table>";
        $receiptContent .= "<h4 class='centered'>***THANKS***</h4>";
        return '<style> *{line-height: 0.4cm;}.address{font-size: .9em;} .text-justify{text-align: justify!important;text-justify: inter-word!important;}
    td,th,tr,table {border-top: 0.5px solid black;border-collapse: collapse;}td{padding:4px;}
    td.description,th.description {}
    td.quantity,th.quantity {word-break: break-all;}
    td.price,th.price {word-break: break-all;}
    .centered {text-align: center;align-content: center;}
    .ticket,#print-section {height:100%;width:100%,}
    img {height:100px}
    
    @media print {html, body {
        height:100%;width:100%,
        position:absolute;
       }.hidden-print,.hidden-print * {display: none !important;}
    }</style><div class="ticket">' . $receiptHeader . $receiptContent . '</div>';
    }
    return '';
}

?>