<?php
require_once "database.php";
require_once "semanticsimilarity.php";

$sense = array();
//assuming you've connected to your MySQL db
$word='memory'; //This variable stores the value given through url
$query=mysql_query("SELECT wordid FROM wn_word WHERE lemma='$word' LIMIT 1") or die(mysql_error());

list($idnya) = mysql_fetch_row($query);
echo "Id untuk kata earphone: <strong>$idnya</strong>, tabel wn_word<br>";

/* cari synset id dari tiap hypernim */

$query=mysql_query("SELECT synsetid FROM wn_sense WHERE wordid='$idnya' ORDER BY rank") or die(mysql_error());
//echo mysql_num_rows($query);
$i=1;
while(list($idsyn) = mysql_fetch_row($query)):
//	echo $i."<br>";
	getSense($idsyn);
	$i++;
endwhile;
echo "<pre>";
	print_r($sense);
echo "</pre>";

/*
Fungsi untuk mencari sense yang dipakai (bisa lebih dari 1)
1. Ambil word id dari kata yang dicari dari tabel wn_word
2. Cari tau synsetid, didapat dari tabel wn_sense
3. Untuk menelusuri hypernim dimulai dengan cari synsetid di tabel wn_semlinkref
   cari yang linkid=1
4. Ambil synsetid,wordid hypernim di tabel wn_sense
5. Ambil lemma hypernim tadi di tabel wn_word berdasarkan wordid
*/

function getSense($idsyn=0)
{
	global $sense;
	static $wordids;
	/* ambil synsetid yang kedua (hubungannya) */
	$query=mysql_query("SELECT synset2id FROM wn_semlinkref WHERE synset1id='$idsyn' AND linkid='1'");
	list($synsetid) = mysql_fetch_row($query);

	/* ambil seluruh wordid untuk synsetid ini */
	$queryWordid = mysql_query("SELECT wordid FROM wn_sense WHERE synsetid='$synsetid'");
	//echo mysql_num_rows($queryWordid);exit();
	$jumlah = mysql_num_rows($queryWordid);
	if(mysql_num_rows($queryWordid)<1):
		$sense[] = $wordids;
		$wordids = array();
		return false;
	else:
		/* logika untuk keluar looping */
		while(list($wordid) = mysql_fetch_row($queryWordid)):
			$tempsId[] = $wordid;
		endwhile;
		$wordids[] = $tempsId;
		getSense($synsetid);
	endif;
}	