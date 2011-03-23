<?php

class DB
{
	private static $conn;
	private $mysql = array();
	private $server;
	private $user;
	private $password;
	private $db;

	/** 
	 * Função de construção só pode ser chamada
	 * pela função getInstancia()
	 */
    private function __construct() {
		require_once('conf_database.ini.php');
		require_once('config.ini.php');
		$id_config = ID_CONFIG_DB;
		$this->setServer($conf[$id_config]['server']);
		$this->setUser($conf[$id_config]['user']);
		$this->setPassword($conf[$id_config]['password']);
		$this->setDatabase($conf[$id_config]['database']);
	}
	
	/** 
	 * Seta e resgata, respectivamente o server
	 */
	public function setServer($server) {
		$this->server = $server;
	}
	public function getServer() {
		return $this->server;
	}
	
	/** 
	 * Seta e resgata, respectivamente o user
	 */
	public function setUser($user) {
		$this->user = $user;
	}
	public function getUser() {
		return $this->user;
	}
	
	/** 
	 * Seta e resgata, respectivamente a password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}
	public function getPassword() {
		return $this->password;
	}
	
	/** 
	 * Seta e resgata, respectivamente o nome do database de dados
	 */
	public function setDatabase($database) {
		$this->database = $database;
	}
	public function getDatabase() {
		return $this->database;
	}
	
	/** 
	 * Abre conexao
	 */
	public static function open($database = 'mysql') {
		if (empty(self::$conn)) {
			self::$conn = new self;
			self::$conn->conecta();
		}
		return self::$conn;
	}
	
	/** 
	 * Conecta com o database de dados
	 */
	private function conecta() {
		$this->conexao = mysql_connect($this->getServer(), $this->getUser(), $this->getPassword());
		mysql_select_db($this->getDatabase(), $this->conexao) or $this->DBError();
		
		$tipo = 'utf8';
		@mysql_query('SET NAMES '.$tipo);
		@mysql_query('SET SESSION character_set_client="'.$tipo.'"');
		@mysql_query('SET SESSION character_set_connection="'.$tipo.'"');
		@mysql_query('SET SESSION character_set_database="'.$tipo.'"');
		@mysql_query('SET SESSION character_set_server="'.$tipo.'"');
		@mysql_query('SET SESSION character_set_results="'.$tipo.'"');
	}

	/** 
	 * Retorna o numero do resultado com query avulsa
	 */
	public function _rows($query) {
		$num = mysql_num_rows($query);
		DB::error();
		return $num;
	}
	
	/** 
	 * Retorna o resultado como objeto com query avulsa
	 */
	public function _object($query) {
		return mysql_fetch_object($query);
	}
	
	/** 
	 * Retorna o resultado como associação com query avulsa
	 */
	public function _assoc($query) {
		return mysql_fetch_assoc($query);
	}
	
	/** 
	 * Retorno o insert id
	 */
	public function _id() {
		return mysql_insert_id();
	}
	
	/** 
	 * Retorna uma query
	 */
	public function query($sql) {
		self::open();
		$query = mysql_query($sql, $this->conexao);
		if (mysql_error()) {
			die($sql.'<br>'.mysql_error());
		}
		return $query;
	}
	
	/** 
	 * Retorna se foi afetada alguma linha do database de dados
	 */
	public function _affected() {
		return mysql_affected_rows();
	}
	
	/** 
	 * Verifica se ocorreu erro
	 */
	public static function error() {
		if (mysql_error()) {
			echo '<h3>Error MySQL: <br />'.mysql_error().'</h3>';
		}
	}
	
	/** 
	 * Fecha conexao abreviada
	 */
	public static function close() {
		if (isset(self::$conn))  {
			self::$conn = NULL;
			return mysql_close();
		}
	}
}

?>