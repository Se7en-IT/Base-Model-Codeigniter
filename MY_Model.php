<?php
/**
 * A base model to provide the basic CRUD 
 * actions for all models that inherit from it.
 *
 * @package CodeIgniter
 * @subpackage MY_Model
 * @link http://github.com/Se7en-IT/Base-Model-Codeigniter
 * @author Luca Musolino <http://lucamusolino.it>
 * @copyright Copyright (c) 2012, Luca Musolino <http://lucamusolino.it>
 */

class MY_Model extends CI_Model
{
    
    /**
     * The database table name
     *
     * @var string
     */
    protected $_table;
        
    /**
     * The primary key, by default set to `id`
     *
     * @var string
     */
    protected $primary_key = 'id';
    
     /**
     * An array of validation rules
     *
     * @var array
     */
    protected $validate = array();

  
    /**
     * The class constructer
     * 
     */
    public function __construct()
    {
        parent::__construct();                
        
    	if (empty($this->_table))
        {
        	//tries to guess for table name
        	        
        	$this->load->helper('inflector');
        	
            $class_name = preg_replace('/(_m|_model)?$/', '', get_class($this));
            
            $this->_table = plural(strtolower($class_name));
        }       
    }
    
    
  	public function __call($name, $args)
    {
    	if (preg_match('/^(find|update|delete)$/', $name, $m) AND count($m) == 2)
		{
			$method = $m[1];
			
			return call_user_func_array(array($this, $method."_by_".$this->primary_key),$args);
		}
		
    	if (preg_match('/^(find|update|delete)_by_([^)]+)$/', $name, $m) AND count($m) == 3)
		{
			$method = $m[1];
			$field = $m[2];
			
			$values = array_shift($args);			
			
			array_unshift($args,array($field=>$values));
						
			return call_user_func_array(array($this, $method."_where"),$args);
		}		    	
		
		if (preg_match('/^(find|update|delete)_where$/', $name, $m) AND count($m) == 2)
		{
			$method = $m[1];
				
			$params = array_shift($args);
			
			foreach($params as $field=>$values){
				if(is_array($values)){
					$this->where_in($field,$values);	
				}else{
					$this->where($field,$values);
				}
			}						
			
			return call_user_func_array(array($this, "_".$method),$args);
		}	
		
		if (method_exists($this->db, $name)){
			return call_user_func_array(array($this->db,$name),$args);
		}
    }
    
    
  	/**
     * Insert a new record into the database,
     * calling the before and after create callbacks.
     * Returns the insert ID.
     *
     * @param array $data Information
     * @return integer
     */
    public function save($data, $skip_validation = FALSE)
    {
    	$data=(object)$data;
    	if(!empty($data->{$this->primary_key})){
    		return $this->update($data->{$this->primary_key}, $data, $skip_validation);
    	}else{
    		return $this->insert($data, $skip_validation);
    	}
    }
    
     /**
     * Insert a new record into the database,
     * calling the before and after create callbacks.
     * Returns the insert ID.
     *
     * @param array $data Information
     * @return integer
     */
    public function insert($data, $skip_validation = FALSE)
    {
    	$data =  $this->trigger_event('before_insert', array($data));
    	
        $valid = ($skip_validation)?TRUE:$this->_run_validation($data);
        
        if ($valid)
        {
            $this->db->insert($this->_table, $data);
            $this->trigger_event('after_insert', array( $data, $this->db->insert_id() ));
            
            return $this->db->insert_id();
        } 
        else 
        {
            return FALSE;
        }
    }
    
   
  	/**
     * Get all records in the database
     *
     * @return array
     */
    private function _find($limit=FALSE,$offset=FALSE)
    {
        $this->trigger_event("before_find");
        
        $result = $this->db->get($this->_table,$limit,$offset)
                            ->result();
                
        $this->trigger_event("after_find",array($result));

        return $result;
    }
    
    /**
     * Update a record, specified by an ID.
     *
     * @param integer $id The row's ID
     * @param array $array The data to update
     * @return bool
     */
    private function _update($data, $skip_validation = FALSE)
    {
      	$data =  $this->trigger_event('before_update', array($data));
    	
      	$valid = ($skip_validation)?TRUE:$this->_run_validation($data);
           
        if ($valid)
        {
            $result = $this->db->set($data)->update($this->_table);
            $this->trigger_event('after_update', array( $data, $result ));
            
            return $result;
        } 
        else
        {
            return FALSE;
        }
    }
    
    /**
     * Delete a row from the database table by the
     * ID.
     *
     * @param integer $id 
     * @return bool
     */
    private function _delete()
    {
       	$this->trigger_event('before_delete');
        $result = $this->db->delete($this->_table);
        $this->trigger_event('after_delete', array( $result ));
        
        return $result;
    }
    
   
    
    /**
     * Run the after_ callbacks, each callback taking a $data
     * variable and returning it
     */
    private function trigger_event($method, $params = array())
    {
		return method_exists($this, $method)?
        	call_user_func_array(array($this, $method), $params):
        	FALSE;            
    }
    
    /**
     * Runs validation on the passed data.
     *
     * @return bool
     */
    private function _run_validation($data)
    {
        if(!empty($this->validate))
        {
            foreach($data as $key => $val)
            {
                $_POST[$key] = $val;
            }
            
            $this->load->library('form_validation');
            
            if(is_array($this->validate))
            {
                $this->form_validation->set_rules($this->validate);
                
                return $this->form_validation->run();
            }
            else
            {
                return $this->form_validation->run($this->validate);
            }
        }
        else
        {
            return TRUE;
        }
    }
}
