<?php
require_once "database.php";
require_once "semanticsimilarity.php";

use \Desertlion\semanticsimilarity as SS;
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Semantic Web &raquo; Using Wordnet Database</title>
	<style>
		* { margin: 0 auto; padding: 0;	}
		body { font-family: sans-serif; font-size: 14px; padding: 35px; background: #333; }
		#container { background: #efefef; width: 70%; padding: 20px; border-radius: 5px; box-shadow: 0 2px 3px rgba(0,0,0,.3); }
		table { width: 100%; border-collapse: collapse; }
		td { border: 1px solid #ddd; padding: 5px 7px; vertical-align: top; }
	</style>
</head>
<body>
	<div id="container">
		<?php 
		$word = new SS('memory'); 
		$kata[0][wordid] = $word->getWordId();
		$kata[0][synsetid] = $word->getSynsetId($kata[0][wordid]);
		$kata[0][definition] = $word->getDefinition($kata[0][synsetid]);
		$kata[0][hypernim] = $word->getHypernim($kata[0][wordid]);
		?>
		<pre><?php var_dump($kata2[0][hypernim]); ?></pre>
		<table>
			<tr>
				<td width="30%">Kata acuan</td>
				<td><strong>Memory</strong></td>
			</tr>
			<tr>
				<td>Wordid</td>
				<td><strong><?php echo $kata[0][wordid]; ?></strong></td>
			</tr>
			<tr>
				<td>Synonim Set Id</td>
				<td><pre><?php echo var_dump($kata[0][synsetid]); ?></pre></td>
			</tr>
			<tr>
				<td>Set Hypernim</td>
				<td>
					<pre><?php var_dump($kata[0][hypernim]); ?></pre>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>