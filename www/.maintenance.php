<?php

header('HTTP/1.1 503 Service Unavailable');
header('Retry-After: 300'); // 5 minutes in seconds

?>
<!DOCTYPE html>
<meta charset="utf-8">
<meta name="robots" content="noindex">

<style>
	body { color: #333; background: white; width: 500px; margin: 100px auto }
	h1 { font: bold 47px/1.5 sans-serif; margin: .6em 0 }
	p { font: 21px/1.5 Georgia,serif; margin: 1.5em 0 }
</style>

<title>Aplikace se dočasně nedostupná</title>

<h1>Pardon</h1>

<p>Webová aplikace je momentálně pod údržbou. Zkuste to prosím za chvíli.</p>

<?php

exit;
