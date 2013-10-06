<?php
namespace Desertlion;

class SemanticSimilarity{
	
	private $_DB;
	private $_kata;

	public function __construct($kata)
	{
		$this->_DB = new \mysqli('localhost','testing','testing','wordnet21') or die("Can't Connect to DB");
		$this->_kata = $kata;
	}

	public function getWordId()
	{
		$query = $this->_DB->query("SELECT wordid FROM wn_word WHERE lemma='$this->_kata' LIMIT 1");
		$result = $query->fetch_object();
		return $result->wordid;
	}

	public function getSynsetId($wordid)
	{
		$hasil = array();
		$query = $this->_DB->query("SELECT synsetid FROM wn_sense WHERE wordid='$wordid' ORDER BY rank");
		while($result = $query->fetch_object()):
			$hasil[] = $result->synsetid;
		endwhile;
		return $hasil;
	}
	public function getDefinition($synsetid)
	{
		$query = $this->_DB->query("SELECT definition FROM wn_synset WHERE synsetid='$synsetid' LIMIT 1");
		$result = $query->fetch_object();
		return $result->definition;
	}

	/*

	Fungsi untuk mencari sense yang dipakai (bisa lebih dari 1)
	1. Ambil word id dari kata yang dicari dari tabel wn_word
	2. Cari tau synsetid, didapat dari tabel wn_sense
	3. Untuk menelusuri hypernim dimulai dengan cari synsetid di tabel wn_semlinkref
	   cari yang linkid=1
	4. Ambil synsetid,wordid hypernim di tabel wn_sense
	5. Ambil lemma hypernim tadi di tabel wn_word berdasarkan wordid

	setiap kata memiliki synonim set yang disebut synset
	setiap synset bisa jadi memiliki beberapa grup
	dalam setiap grup memiliki beberapa gloss
	dalam satu gloss bisa jadi terdapat beberapa wordid

	*/

	public function getHypernim($idnya)
	{
		/* definisikan word id yang akan dicari hypernim nya */
		$wordid = $idnya;

		/* setelah dapat, cari semua synonim set nya */
		$synsetids = $this->getSynsetId($wordid); // <-- outputnya beberapa synonim set aaaaaa, bbbbbb, cccccc

		/* buat variabel untuk menampung semua hypernim */
		$hypernim = array();

		/* 
			dari semua synset tadi, cari grup-grup nya, masukkan dalam masing-masing synset
			strukturnya itu
			synset[0..n] = grupSynset{wordids}
		*/
		
		foreach($synsetids as $id):

			//posisi: didalam 1 sense
			/*
			$hypernim[$id] = array(
				'grup' = array(
					'gloss' = array(
						array()
					),
				),
			);
			*/

			$groups = $this->getSynset2Id($id);
			if(count($groups)>0):
				$i=0;
				foreach($groups as $grupid):
					//return var_dump($grupid);
					//ambil dulu gloss untuk id yang pertama, stelah itu cari relasinya
					$gloss = $this->getWordIdFromSynsetId($grupid); 
					$hypernim[$id][$i][] = $gloss;
					//return $hypernim;
					//mulai cari relasinya
					$synsetid = $grupid;
					while($synsetid>0):
						$synset2id = $this->getSynset2Id($synsetid); //cari synset2id dari grupid nya, setelah itu baru cari gloss
						if(count($synset2id)>0):
							$gloss = $this->getWordIdFromSynsetId($synset2id[0]);
							$hypernim[$id][$i][] = $gloss;
							//return ($synsetid==105433574) ? $synset2id : '';
							$synsetid = $synset2id[0];
						else:
							$i=0;
							$synsetid = 0;
						endif;
					endwhile;
					$i++;
				endforeach;
			endif;

			/*
			$groups[] = $this->getSynset2Id($id); //<-- menghasilkan bbrp grup gloss (synset2id)
			
			//setelah dapat synset grup dari synset utama nya, looping
			//untuk mendapatkan word id nya
			//kemudian simpan di dalam gloss
			foreach($groups as $groupId): //<-- groupId (synset2id)

				//untuk grup ini mulai kita cari gloss2 nya
				//untuk mencari gloss berikutnya, harus tau dulu synsetid berikutnya
				//cari di tabel semlinkref
				

			endforeach;*/

		endforeach;

		return $hypernim;

	}

	public function getSynset2Id($synset1id)
	{
		$query=$this->_DB->query("SELECT synset2id FROM wn_semlinkref WHERE synset1id='$synset1id' AND linkid='1'");
		if($query->num_rows>0):
			while($result = $query->fetch_object()):
				$hasil[] = $result->synset2id;
			endwhile;
			return $hasil;
		else:
			return false;
		endif;
	}

	public function getWordIdFromSynsetId($synsetid)
	{
		$query = $this->_DB->query("SELECT wordid FROM wn_sense WHERE synsetid='$synsetid'");
		if($query->num_rows>0):
			while($result = $query->fetch_object()):
				$hasil[] = $result->wordid;
			endwhile;
			return $hasil;
		else:
			return false;
		endif;
	}
}