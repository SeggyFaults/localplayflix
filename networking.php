<?php

//for the initial connection as well as data retrieval

class databaseConnection{
	public $pdo;
	
	function __construct(){
	
	}
	
	//mysql initial connection
	function connect(){
		$host = '';
		$db = '';
		$user = 'root';
		$pass = '';
		$charset = 'utf8';

		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		$opt = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];

		$this->pdo = new PDO($dsn, $user, $pass, $opt);
	}
	
	//generic insert, by 2 arrays of names and values and then the table name
	function insert(array $names, array $values, string $table){
		$names = implode(", ", $names);
		$qs = "?";
		for($i = 1; $i < count($values); $i++){
			$qs .= ", ?";
		}
		//echo $qs . PHP_EOL;
		$query = "INSERT INTO $table ($names) values ($qs)";
		//echo $query . PHP_EOL;
		$stmt = $this->pdo->prepare($query);
		$stmt->execute($values);
	}
	
	//at the moment there doesn't seem to be a decent way of generalizing this. insert is pretty formulaic, these, not so much
	function select(){
		
	}
	
	function update(){
	
	}
	
	
	//lookup section
	
	function retrievePost(){
	
	}
	
	function retrievePostsById(){
	
	}
	
	function retrievePostsByDate(){
	
	}
	
	function retrievePostsByAuthor(){
	
	}
	
	function retrievePostsByMeta(){
	
	}
	
	function getCurrentMetaKey(){
		return $this->pdo->query("SELECT MAX(metakey) as 'maxmetakey' FROM posts")->fetch()["maxmetakey"];
	}


	
	
	
	
}

class Post{
	public $dbc;
	public $title;
	public $content;
	private $author;
	private $authorid;
	public $metakey;
	public $metadata;
	
	//pass dbc by reference to save on performance
	function __construct(&$dbc, $title, $content){
		$this->title = $title;
		$this->content = $content;
		$this->dbc = $dbc;
		$this->author = $_SESSION["username"];
		$this->authorid = $_SESSION["userid"];
	}
	
	function createPost(){
		if($_SESSION["admin"] === 1){
		
			//TODO get autoincrement
			$this->metakey = $this->dbc->getCurrentMetaKey() + 1; //TODO do this earlier, as constructor, allows for meta creation
			//$m = $metakey->fetch()["maxmetakey"];
			//TODO add user security check
			
			
			$this->dbc->insert(array("title", "content", "author", "authorid", "metakey", "postdate"), array($this->title, $this->content, $this->author, $this->authorid, $this->metakey, date("Y-m-d H:i:s")), "posts");
			//TODO meta insert thing, gotta get a better way of architecting that. I have a headache rn, Ill do that later
			
			
		}else{
			echo "wrong " . $_SESSION['admin'] . "<br>";
		}
		
	}
	
	function updatePost(){
	
	}
	
	function deletePost(){
	
	}
}

class Meta{
	public $dbc;
	public $entries;
	public $metakey;
	//TODO restructure 
	
	function __construct(&$dbc, $metakey){
		$this->dbc = $dbc;
		$this->metakey = $metakey;
		$this->entries = array();
	}
	
	function addMeta($k, $v){
		$this->entries[] = [$k, $v];
	}
	
	function createMeta($k, $v){
		$this->dbc->insert(array("metakey", "k", "v"), array($this->metakey, $k, $v), "meta");
	}
	
	function createMultipleMeta($entries){
		foreach($entries as $entry){
			$this->createMeta($entry[0], $entry[1]);
		}
	}
	
	function updateMeta(){
	
	}
	
	function removeMeta(){
	
	}
	

}

class User{
	//TODO UNSECURE AS HELL DO NOT LAUNCH TODO TODO TODO
	public $dbc;
	
	function __construct(&$dbc){
		$this->dbc = $dbc;
	}
	
	function login($username, $userid, $password){
		$stmt = $this->dbc->pdo->prepare("SELECT * from users where userid = ?");
		$stmt->execute([$userid]);
		$result = $stmt->fetch();
		if($password === $result["password"]){
			session_start();
			$_SESSION = $result;
			
			//$_SESSION["username"] = $username;
			//$_SESSION["userid"] = $userid;
			var_dump($_SESSION);
		}else{
			echo "wrong<br>";
		}
		
	}
	
	function logout(){
		session_unset();
		session_destroy();
	}

}

?>