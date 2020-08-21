<?php
require '../gapi.class.php';
define('ga_profile_id','profile_id');

$ga = new gapi("XXXXXXXX@XXXXXXXXXX.iam.gserviceaccount.com", "key.p12");

$ga->requestReportData(ga_profile_id,array('browser','browserVersion'),array('pageviews','visits'));
?>
<table>
<tr>
  <th>Browser &amp; Browser Version</th>
  <th>Pageviews</th>
  <th>Visits</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
  <td><?php echo $result->getPageviews() ?></td>
  <td><?php echo $result->getVisits() ?></td>
</tr>
<?php
endforeach
?>
</table>

<table>
<tr>
  <th>Total Results</th>
  <td><?php echo $ga->getTotalResults() ?></td>
</tr>
<tr>
  <th>Total Pageviews</th>
  <td><?php echo $ga->getPageviews() ?>
</tr>
<tr>
  <th>Total Visits</th>
  <td><?php echo $ga->getVisits() ?></td>
</tr>
</table>
