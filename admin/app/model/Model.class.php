<?php

class Model
{
	/**
	 * Variaveis
	 */
	protected $table;
	protected $id_table = 'id';
	protected $fields;
	protected $is_null;
	protected $cond = array();
	protected $join = array();
	protected $fields_select = array();
	protected $valid_order = array();
	protected $default_order = 'id DESC';
	protected $group_by;
	protected $dpp = 20;
	protected $strip_tags;
	protected $total;
	protected $_filters = array();
	protected $_filters_get = array();
	protected $remove_empty = array();
	private   $data;
	private   $debug;
	protected $load_all_ = false;
	public 	  $active_search = true;

	/**
	 * Construtor
	 */
	public function __construct() {
		// Abre conexao
		$this->db 				= DB::open();
		$this->fields 			= $this->getFields(false);
		$this->fields_buscar 	= $this->fields;
		$this->strip_tags		= true;
	}
	
	/**
	 * Resgata debug
	 */
	public function getDebug() {
		return $this->debug;
	}
	
	/**
	 * Seta condicao
	 */
	public function setCond($cond) {
		if (!empty($cond)) {
			$this->cond[] = $cond;
		}
	}
	
	/**
	 * Seta join
	 */
	public function setJoin($join) {
		if (!empty($join)) {
			$this->join[] = $join;
		}
	}
	
	/**
	 * Resgata join
	 */
	public function getJoin() {
		return implode(' ', $this->join);
	}
	
	/**
	 * Fields selecionar
	 */
	public function setFieldsSelect($fields) {
		if (!empty($fields)) {
			$this->fields_select = $fields;
		}
	}
		
	/**
	 * Carrega dados
	 */
	public function load($id = null) {
		if (is_numeric($id)) {
			$fields_sel	= count($this->fields_select) ? implode(', ', $this->fields_select) : '*' ;
			
			$sql 	= 'SELECT '.$fields_sel.' ';
			$sql   .= 'FROM '.$this->table.' ';
			$sql   .= $this->getJoin().' ';
			$sql   .= 'WHERE '.$this->id_table.' = "'.intval($id).'"';
			$sql   .= $this->getGroupBy().' ';
			$sql   .= $this->getOrderBy().' ';
			$sql   .= (!$this->load_all_) ? $this->getLimit() : '';
			$this->debug = $sql;
			
			$query 	= $this->db->query($sql);
			if ($line = $this->db->_assoc($query)) {
				$line = $this->applyFiltersGet($line);
				return $line;
			}
		}
		else if (isset($_GET['q']) && $this->active_search) {
			$users 	= array();
			$q			= Filters::anti_sql_injection($_GET['q']);
			$sql_busca 	= array();
			foreach ($this->fields_buscar as $field) {
				$sql_busca[] = $field.' LIKE _utf8 "%'.$q.'%" COLLATE utf8_unicode_ci';
			}
			$fields_sel	= count($this->fields_select) ? implode(', ', $this->fields_select) : '*' ;

			$sql 	= 'SELECT '.$fields_sel.' ';
			$sql   .= 'FROM '.$this->table.' ';
			$sql   .= $this->getJoin().' ';
			$sql   .= 'WHERE '.implode(' OR ', $sql_busca).' ';
			$sql   .= $this->getGroupBy().' ';
			$sql   .= $this->getOrderBy().' ';			
			$query 	= $this->db->query($sql);
			$this->total  = $this->db->_rows($query);
			
			$sql   .= (!$this->load_all_) ? $this->getLimit() : '';
			$this->debug = $sql;
			
			$query 	= $this->db->query($sql);
			
			while ($line = $this->db->_assoc($query)) {
				$line		= $this->applyFiltersGet($line);
				$users[] 	= $line;
			}
			return $users;
		}
		else {
			$all 			= array();
			$fields_sel		= count($this->fields_select) ? implode(', ', $this->fields_select) : '*' ;
			
			$sql 			= 'SELECT '.$fields_sel.' ';
			$sql 		   .= 'FROM '.$this->table.' ';
			$sql 		   .= $this->getJoin().' ';
			if (isset($this->cond) && count($this->cond)) {
				$sql .= 'WHERE '.implode(' AND ', $this->cond).' ';
			}
			$sql   		   .= $this->getGroupBy().' ';
			$sql 		   .= $this->getOrderBy().' ';
			$query 			= $this->db->query($sql);
			$this->total  	= $this->db->_rows($query);
			
			$sql 		   .= (!$this->load_all_) ? $this->getLimit() : '';
			$this->debug = $sql;
			
			$query 			= $this->db->query($sql);
			
			while ($line = $this->db->_assoc($query)) {
				$line			= $this->applyFiltersGet($line);
				$all[] 			= $line;
			}
			return $all;
		}
		return false;
	}
		
	/**
	 * Carrega todos dados
	 */
	public function load_all($id = null) {
		$this->load_all_ = true;
		return $this->load($id);
	}
	
	/**
	 * Aplica filters ao carregar dados
	 */
	public function applyFiltersGet ($line) {
		$new_line = array();
		foreach ($line as $field => $value) {
			$treated_value = $value;
			if (array_key_exists($field, $this->_filters_get)) {
				$filter 		= $this->_filters_get[$field];
				$all_filters 	= get_class_methods('Filters');
				if (function_exists($filter)) {
					$treated_value = $filter($treated_value);
				}
				else if (in_array($filter, get_class_methods($this))) {
					$treated_value = $this->$filter($treated_value);
				}
				else if (in_array($filter, $all_filters)) {
					$treated_value = Filters::$filter($treated_value);
				}
			}
			else if (array_key_exists('all', $this->_filters_get)) {
				$filter = $this->_filters_get['all'];
				$treated_value = Filters::$filter($treated_value);
			}
			$new_line[$field] = $treated_value;
		}
		return $new_line;
	}
	
	/**
	 * Carrega dados da lixeira
	 */
	public function loadTrash() {
		if (in_array('active', $this->fields)) {
			$data_load	= array();
			$sql 	= 'SELECT * FROM '.$this->table.' WHERE active = "0"';
			$query 	= $this->db->query($sql);
			while ($line = $this->db->_assoc($query)) {
				$data_load[] = $line;
			}
			return $data_load;
		}
	}
	
	/**
	 * Total registros sem pagecao
	 */
	public function count() {
		if (isset($this->total)) {
			return $this->total;
		}
		else {
			$sql 	= 'SELECT * FROM '.$this->table;
			$query 	= $this->db->query($sql);
			return $this->db->_rows($query);
		}
	}
	
	/**
	 * Total registros sem pagecao
	 */
	public function countTrash() {
		if (in_array('active', $this->fields)) {
			$sql 	= 'SELECT * FROM '.$this->table.' WHERE active = "0"';
			$query 	= $this->db->query($sql);
			return $this->db->_rows($query);
		}
	}
	
	/**
	 * Insere dados
	 */
	public function insert() {
		$data = $this->getData();
		if (isset($this->is_null) && count($this->is_null)) {
			foreach ($this->is_null as $key => $value) {
				if (isset($data[$key]) && $data[$key] == '""' && $value == 'NO') {
					$url = ereg_replace('&msg=.*$', '', $_SERVER['HTTP_REFERER']);
					$url = !ereg('\?', $url) ? $url.'?' : $url;
					header('Location: '.$url.'&msg=dados_incompletos');
					//Functions::debug($data);
					exit;
				}
			}
		}
		
		$sql  	 = 'INSERT INTO '.$this->table.' ';
		$sql 	.= '('.implode(', ', $this->fields).') ';
		$sql 	.= 'VALUES ';
		$sql 	.= '('.implode(', ', $this->getData()).') ';
		$this->debug = $sql;
		
		$query 	 = $this->db->query($sql);
		return $query;
	}
	
	/**
	 * Atualiza dados
	 */
	public function update($id = null) {
		$data 		= $this->getData();
		$values 	= array();

		foreach ($data as $field => $value) {
			$values[] = $field.' = '.$value;
		}
		
		$sql  	 = 'UPDATE '.$this->table.' SET ';
		$sql 	.= implode(', ', $values).' ';
		if (isset($this->cond) && count($this->cond)) {
			$sql 	.= 'WHERE '.implode(' AND ', $this->cond);
		}
		else {
			$sql 	.= 'WHERE '.$this->id_table.' = "'.$id.'"';
		}
		$this->debug = $sql;
		
		$query 	 = $this->db->query($sql);
		return $query;
	}
	
	/**
	 * Envia para lixeira
	 */
	public function trash($id) {
		if (in_array('active', $this->fields)) {
			$sql  	 = 'UPDATE '.$this->table.' ';
			$sql 	.= 'SET ';
			$sql 	.= 'active = "0" ';
			$sql 	.= 'WHERE '.$this->id_table.' = '.intval($id);
			$query 	 = $this->db->query($sql);
			return $query;
		}
	}
	
	/**
	 * Restaurar registro da lixeira
	 */
	public function restore($id) {
		if (in_array('active', $this->fields)) {
			$sql 	= 'UPDATE '.$this->table.' SET active = "1" WHERE '.$this->id_table.' = '.intval($id);
			$query 	= $this->db->query($sql);
			return $query;
		}
	}
	
	/**
	 * Deletar
	 * @var id = id da table
	 * @var all_trash = deletar toda lixeira
	 */
	public function delete($id = null, $all_trash = false) {
		$sql  	 = 'DELETE FROM '.$this->table.' ';
		
		if (isset($this->cond) && count($this->cond)) {
			$sql 	.= 'WHERE '.implode(' AND ', $this->cond);
		}
		else {
			if ($all_trash == false) {
				$sql 	.= 'WHERE '.$this->id_table.' = '.intval($id);
			}
			else {
				$sql 	.= 'WHERE active = "0"';
			}
		}
		$this->debug = $sql;
		
		$query 	 = $this->db->query($sql);
		return $query;
	}
	
	/**
	 * Seta data
	 */
	public function setData($data = null) {
		$data = is_null($data) ? $_POST : $data;
		if (is_object($data)) {
			$this->data = (array) $data;
		}
		else if (is_array($data)) {
			$this->data = $data;
		}
	}
	
	/**
	 * Get data para insercao
	 */
	public function getData() {
		// Deletando fields vazios
		if (count($this->remove_empty)) {
			foreach ($this->remove_empty as $field) {
				if (array_key_exists($field, $this->data) && $this->data[$field] == '') {
					$key = array_search($field, $this->fields);
					unset($this->fields[$key]);
				}
			}
		}
		
		// Aplicando formatação aos valores
		$values = array();
		foreach ($this->fields as $field) {
			if (array_key_exists($field, $this->data)) {
				$treated_value 		= Filters::anti_sql_injection($this->data[$field]);
				$treated_value 		= ($this->strip_tags) ? Filters::strip_tags($treated_value) : $treated_value;
				if (array_key_exists($field, $this->_filters)) {
					$filter 		= $this->_filters[$field];
					$all_filters 	= get_class_methods('Filters');
					if (function_exists($filter)) {
						$treated_value = $filter($treated_value);
					}
					else if (in_array($filter, get_class_methods($this))) {
						$treated_value = $this->$filter($treated_value);
					}
					else if (in_array($filter, $all_filters)) {
						$treated_value = Filters::$filter($treated_value);
					}
				}
				$values[$field] 	= '"'.$treated_value.'"';
			}
			else {
				$values[$field] = '""';
			}
		}
		return $values;
	}
	
	/**
	 * Get data para atualizacao
	 */
	public function getDataUpdate() {
		$values = array();
		foreach ($this->getData() as $field => $value) {
			$values[] 	= $field.' = '.$value;
		}
		return $values;
	}
	
	/**
	 * Seta order do select
	 */
	public function setOrderBy($order) {
		$this->default_order = $order;
	}
	
	/**
	 * Resgata order do select
	 */
	public function getOrderBy() {
		$order = isset($_GET['order']) ? $_GET['order'] : '';
		if (in_array(str_replace(' desc', '', $order), $this->valid_order)) {
			$order_by = ' ORDER BY '.$order;
		}
		else {
			$order_by = ' ORDER BY '.$this->default_order;
		}
		return $order_by;
	}
	
	/**
	 * Resgata limit
	 */
	public function getLimit() {
		$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
		$page = ($page - 1) * $this->dpp;
		return 'LIMIT '.$page.', '.$this->dpp;
	}
	
	/**
	 * Seta nomes manuais
	 */
	public function setFields ($array) {
		$this->fields = $array;
	}
	
	/**
	 * Resgata nome dos fields da table dinamicamente
	 */
	public function getFields ($id = true) {
		if (isset($this->table)) {
			$columns = array();
			$sql = 'SHOW COLUMNS FROM '.$this->table;
			$query = $this->db->query($sql);
			while ($line = $this->db->_object($query)) {
				$columns[] = $line->Field;
				$this->is_null[$line->Field] = $line->Null;
			}
			if (!$id) {
				unset($columns[0]);
			}
			return $columns;
		}
		else {
			die('Not informed table.');
		}
	}
	
	/**
	 * Resgata id 
	 */
	public function getId () {
		return mysql_insert_id();
	}
	
	/**
	 * Aplica filters aos dados obtidos no banco de dados
	 */
	public function addFilter ($data, $filters) {
		$new_data_global = array();
		$new_data = array();
		foreach ($data as $_data) {
			if ((is_array($_data) && count($_data)) || is_object($_data)) {
				foreach ($_data as $field => $value) {
					if (array_key_exists($field, $filters)) {
						$all_filters = get_class_methods('Filters');
						if (in_array($filters[$field], $all_filters)) {
							$new_data[$field] = Filters::$filters[$field]($value);
						}
					}
					else if (array_key_exists('all', $filters)) {
						$all_filters = get_class_methods('Filters');
						if (in_array($filters['all'], $all_filters)) {
							$new_data[$field] = Filters::$filters['all']($value);
						}
					}
					else {
						$new_data[$field] = $value;
					}
				}
				$new_data_global[] = $new_data;
			}
		}
		return $new_data_global;
	}
	
	/**
	 * Seta dpp
	 */
	public function setDpp ($dpp) {
		$this->dpp = $dpp;
	}
	
	/**
	 * Resgata dpp
	 */
	public function getDpp () {
		return $this->dpp;
	}
	
	/**
	 * Esvazia table
	 */
	public function truncate () {
		$sql = 'TRUNCATE TABLE '.$this->table;
		$query = $this->db->query($sql);
		return $query;
	}
	
	/**
	 * Seta o group by
	 */
	public function setGroupBy ($group) {
		$this->group_by = $group;
	}
	
	/**
	 * Resgata o group by
	 */
	public function getGroupBy () {
		$group = $this->group_by ? 'GROUP BY '.$this->group_by : '';
		return $group;
	}
	
}

?>