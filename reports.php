<?php
session_start();
require_once("config.inc.php");
require_once($yaptc_inc . "functions.inc.php");
$yaptc_pagename = "Reports";
require_once($yaptc_inc . "header.inc.php");
require_once($yaptc_inc . "menu.inc.php");
if (getSessionStatus() == false):
killSession();
else: ?>
<!-- ********** BEGIN CONTENT ********** -->

<?php if ($session_user["0"]["usertype"] == "Administrator"): ?>
<h2 class="content-subhead">History Reports</h2>
<p>The drop-down below can be used to select pre-configured reports.  Other reports are currently being written.</p>
<form class="pure-form pure-form-stacked" action="reports.php" method="post">
<fieldset>
<div class="pure-g">
<div class="pure-u-1">
<label for="reporttype">Report Type</label>
<select name="reporttype" class="pure-input-1-2">
<?php if (isset($_POST['reporttype'])): ?>
    <option value="<?php echo $_POST['reporttype']; ?>"><?php echo $_POST['reporttype']; ?></option>
    <option>----------</option>
<?php else: ?>
    <option></option>
<?php endif; ?>
<option value="Hours per week per user">Hours per week per user</option>
<option value="Hours per month per user">Hours per month per user</option>
<option value="All Punches">All Punches</option>
</select>
</div>
</div>
<button type=\"submit\" class=\"pure-button pure-button-primary\">Submit</button>
</fieldset>
</form>


<?php if (isset($_POST['reporttype'])): ?>
    <?php if ($_POST['reporttype'] == "Hours per week per user"): ?><table class="pure-table pure-table-striped">
        <thead><tr><th>Year</th><th>Week#</th><th>Name</th><th>Hours</th></tr></thead>
        <tbody><?php foreach (reportWeeklyByUser($yaptc_db) as $row): ?>
        <tr><td><?php echo $row['g_year']; ?></td><td><?php echo $row['g_week']; ?></td><td><?php echo $row['lastname'] . ", " . $row['firstname']; ?></td><td><?php echo $row['punchhours']; ?></td></tr><?php endforeach; ?>
        </tbody>
        </table>
    <?php endif; ?>
    <?php if ($_POST['reporttype'] == "Hours per month per user"): ?><table class="pure-table pure-table-striped">
        <thead><tr><th>Year</th><th>Month</th><th>Name</th><th>Hours</th></tr></thead>
        <tbody><?php foreach (reportMonthlyByUser($yaptc_db) as $row): ?>
        <tr><td><?php echo $row['g_year']; ?></td><td><?php echo $row['g_month']; ?></td><td><?php echo $row['lastname'] . ", " . $row['firstname']; ?></td><td><?php echo $row['punchhours']; ?></td></tr><?php endforeach; ?>
        </tbody>
        </table>
    <?php endif; ?>
    <?php if ($_POST['reporttype'] == "All Punches"): ?><table class="pure-table pure-table-striped">
        <thead><tr><th>In</th><th>Out</th><th>Name</th><th>Hours</th><th>Flagged</th><th>Notes</th></tr></thead>
        <tbody><?php foreach (listPunches($db, "%") as $row): ?>
        <tr><td><?php echo $row['intime']; ?></td><td><?php echo $row['outtime']; ?></td><td><?php echo $row['lastname'] . ", " . $row['firstname']; ?></td><td><?php echo $row['punchhours']; ?></td><td><?php echo $row['modified']; ?></td><td><?php echo $row['notes']; ?></td></tr><?php endforeach; ?>
        </tbody>
        </table>
    <?php endif; ?>
<?php else: ?>
    <p>No query to display.  Please select from the dropdown above...</p>
<?php endif; ?>
<?php else: ?>
<h2 class="content-subhead">NOT AUTHORIZED!</h2>
<?php endif; ?>

<!-- ********** END CONTENT ********** -->
<?php endif; require_once($yaptc_inc . "footer.inc.php"); ?>
