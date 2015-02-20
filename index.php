<?php
session_start();
require_once("config.inc.php");
$yaptc_pagename = "Home";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else:
//********** BEGIN CONTENT **********// ?>

<?php
$userid = $_SESSION['user_id'];





// This is to get the current user status - in or out - and the notes and times associated for use in the form
$result = $yaptc_db->prepare("SELECT punches.id as punchid, users.id as user, punches.intime as intime, punches.outtime as outtime, punches.notes as notes FROM punches INNER JOIN users ON punches.userid = users.id WHERE users.id = $userid ORDER BY punches.id DESC LIMIT 1");
    $result->execute();
    $last = $result->fetchObject();
    
    // Let's build the page - this is the header with current status
    echo "<h2 class=\"content-subhead\">Current Status</h2>";
    if (!isset($last->user)) {
        echo "<p>You do not appear to have any punches on record.</p>";
        $status = "Out";
    } //!isset($last->user)
    else {
        if (!empty($last->outtime)) {
            $status     = "Out";
            $statustime = $last->outtime;
        } //!empty($last->outtime)
        else {
            $status     = "In";
            $statustime = $last->intime;
            $punchid    = $last->punchid;
            $notes      = $last->notes;
        }
        echo "<p>You have been Punched $status since " . date('g:i a \o\n M jS, Y', strtotime($statustime)) . ".</p>";
    }
    echo "<h2 class=\"content-subhead\">Quick Punch</h2>";
    
    echo "<p>Clicking the button below will immediately enter a new punch for you depending on your current status.  Any notes you enter will be attached to the punch for your administrator to review.</p>";
    echo "<form class=\"pure-form pure-form-stacked\" action=\"index.php\" method=\"post\">";
    echo "<fieldset>";
    if (isset($notes)) {
        echo "<input class=\"pure-input-1\" type=\"text\" name=\"notes\" placeholder=\"Enter notes if needed\" maxlength=\"255\" value=\"$notes\">";
    } //isset($notes)
    else {
        echo "<input class=\"pure-input-1\" type=\"text\" name=\"notes\" placeholder=\"Enter notes if needed\" maxlength=\"255\">";
    }
    echo "<div class=\"pure-controls\">";
    if ($status == "In") {
        echo "<button type=\"submit\" class=\"pure-button button-xlarge button-success pure-button-disabled\">Punch IN</button>";
        echo "<button type=\"submit\" class=\"pure-button button-xlarge button-error\">Punch OUT</button>";
    } //$status == "In"
    else {
        echo "<button type=\"submit\" class=\"pure-button button-xlarge button-success\">Punch IN</button>";
        echo "<button type=\"submit\" class=\"pure-button button-xlarge button-error pure-button-disabled\">Punch OUT</button>";
    }
    echo "</div>";
    
    // If the posted variables are not empty, we must be trying to insert a new punch.  Use the form values to insert new record
    if (!empty($_POST)) {
        
        // Is the notes field set? If so, use, otherwise set to null
        if (isset($_POST['notes'])) {
            if (!empty($_POST['notes'])) {
                $p_notes = $_POST['notes'];
            } //!empty($_POST['notes'])
            else {
                $p_notes = NULL;
            }
        } //isset($_POST['notes'])
        else {
            $p_notes = NULL;
        }
        
        // Is the user currently punched in?  If so, insert the punch out record, otherwise, insert a new punch in
        if ($status == "In") {
            $query = "UPDATE punches SET outtime = NOW(), notes = :p_notes WHERE id = :p_punchid";
            $stmt  = $yaptc_db->prepare($query);
            $stmt->execute(array(
                ':p_punchid' => $punchid,
                ':p_notes' => $p_notes
            ));
        } //$status == "In"
        else {
            $query = "INSERT INTO punches (userid, notes, intime) VALUES (:p_userid, :p_notes, NOW())";
            $stmt  = $yaptc_db->prepare($query);
            $stmt->execute(array(
                ':p_userid' => $_SESSION['user_id'],
                ':p_notes' => $p_notes
            ));
        }
        
        // And then send user back to this page to see the updates
        header('Location: ' . $_SERVER['PHP_SELF']);
    } //!empty($_POST)
    
    // Close out the form...
    echo "</fieldset>";
    echo "</form>";
?>    
    

<?php //********** END CONTENT **********//
endif;
require_once($yaptc_inc . "footer.inc.php");
?>
