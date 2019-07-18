<?php
if(!defined('ABSPATH')) {
	exit;
}
?>

<p><?php echo "Let op! Bij bestelling $link zijn de volgende gegevens ingevuld:" ?></p>
<ul>
	<li><strong>Brief ontvangen</strong> <?php echo $receivedLetter ?></li>
	<li><strong>Afgiftedatum rijbewijs</strong> <?php echo $issueDateDrivingLicense ?></li>
</ul>
