<?php

if(isset($_POST['email'])) {
    $result = $this->emailChange($_POST['email']);
    if($result === false) {
        echo $this->lang_php['email_not_found'] . '!';
    } else {
        $url = base_url('login?reset_code=' . $result);
        $to = $_POST['email'];
        $subject = 'Password reset from ' . base_url();
        $message = "Your password reset link is \n $url";

        $sender = mail($to, $subject, $message);
        if($sender == true) {
            echo $this->lang_php['email_reset'] . '!';
        } else {
            echo $this->lang_php['email_reset_err'];
        }
    }
}
?>