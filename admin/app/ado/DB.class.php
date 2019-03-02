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
        $this->conexao = ($GLOBALS["___mysqli_ston"] = mysqli_connect($this->getServer(),  $this->getUser(),  $this->getPassword()));
        mysqli_select_db( $this->conexao, $this->getDatabase()) or $this->DBError();
        
        $tipo = 'utf8';
        @mysqli_query($GLOBALS["___mysqli_ston"], 'SET NAMES '.$tipo);
        @mysqli_query($GLOBALS["___mysqli_ston"], 'SET SESSION character_set_client="'.$tipo.'"');
        @mysqli_query($GLOBALS["___mysqli_ston"], 'SET SESSION character_set_connection="'.$tipo.'"');
        @mysqli_query($GLOBALS["___mysqli_ston"], 'SET SESSION character_set_database="'.$tipo.'"');
        @mysqli_query($GLOBALS["___mysqli_ston"], 'SET SESSION character_set_server="'.$tipo.'"');
        @mysqli_query($GLOBALS["___mysqli_ston"], 'SET SESSION character_set_results="'.$tipo.'"');
    }
    /** 
     * Retorna o numero do resultado com query avulsa
     */
    public function _rows($query) {
        $num = mysqli_num_rows($query);
        DB::error();
        return $num;
    }
    
    /** 
     * Retorna o resultado como objeto com query avulsa
     */
    public function _object($query) {
        return mysqli_fetch_object($query);
    }
    
    /** 
     * Retorna o resultado como associação com query avulsa
     */
    public function _assoc($query) {
        return mysqli_fetch_assoc($query);
    }
    
    /** 
     * Retorno o insert id
     */
    public function _id() {
        return ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
    }
    
    /** 
     * Retorna uma query
     */
    public function query($sql) {
        self::open();
        $query = mysqli_query( $this->conexao, $sql);
        if (mysqli_error($GLOBALS["___mysqli_ston"])) {
            die($sql.'<br>'.mysqli_error($GLOBALS["___mysqli_ston"]));
        }
        return $query;
    }
    
    /** 
     * Retorna se foi afetada alguma linha do database de dados
     */
    public function _affected() {
        return mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
    }
    
    /** 
     * Verifica se ocorreu erro
     */
    public static function error() {
        if (mysqli_error($GLOBALS["___mysqli_ston"])) {
            echo '<h3>Error mysql: <br />'.mysqli_error($GLOBALS["___mysqli_ston"]).'</h3>';
        }
    }
    
    /** 
     * Fecha conexao abreviada
     */
    public static function close() {
        if (isset(self::$conn))  {
            self::$conn = NULL;
            return ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
        }
    }
}
?> 
