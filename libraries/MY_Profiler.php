<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Profiler {

	protected $_available_sections = array('benchmarks',
										   'memory_usage',
										   'database',
										   'request_headers',
										   'request_data',
										   'uri_string',
										   'controller_info',
										   'session_data',
										   'config',
										   'loader');

	protected $_enabled_sections   = array();

	protected $_query_toggle_count = 25;
	protected $_profiler_path = '';

	protected $CI;

	// --------------------------------------------------------------------

	public function __construct($config = array())
	{
		$this->CI =& get_instance();

		if (isset($config['query_toggle_count']))
		{
			$this->_query_toggle_count = (int) $config['query_toggle_count'];
			unset($config['query_toggle_count']);
		}

		if (isset($config['profiler_path']))
		{
			$this->_profiler_path = $config['profiler_path'];
			unset($config['profiler_path']);
		}
		else
		{
			$this->_profiler_path = APPPATH.'profiler/';
		}

		// default all sections to display
		foreach ($this->_available_sections as $section)
		{
			if ( ! isset($config[$section]))
			{
				$this->enable_section($section);
			}
		}

		$this->set_sections($config);
	}

	// --------------------------------------------------------------------

	public function enable_section($name)
	{
		if(in_array($name, $this->_available_sections) AND ! in_array($name, $this->_enabled_sections))
		{
			$this->_enabled_sections[] = $name;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function disable_section($name)
	{
		if($key = array_search($name, $this->_enabled_sections))
		{
			unset($this->_enabled_sections[$key]);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Sections
	 *
	 * Sets the private _compile_* properties to enable/disable Profiler sections
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function set_sections($config)
	{
		foreach ($config as $method => $enable)
		{
			if($enable !== FALSE)
			{
				$this->enable_section($method);
			}
			else
			{
				$this->disable_section($method);
			}
		}
	}

	// --------------------------------------------------------------------

	public function run()
	{
		if( ! $this->_check_environment())
		{
			return '';
		}

		$this->CI->load->helper(array('form','html','array','string','text','url', 'language'));
		$this->CI->load->language('profiler');

		$_data = array(
			'sections'	=> $this->_enabled_sections,
			'body'		=> array()
		);

		if( ! empty($this->_enabled_sections))
		{
			foreach ($this->_enabled_sections as $section)
			{
				if(method_exists($this, "_compile_$section"))
				{
					$_data['body'][$section] = $this->_load_section($section);
				}
			}
		}
		else
		{
			$_data['body'] = $this->lang->line('profiler_no_profiles');
		}

		return $this->_load_view('profiler', $_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Auto Profiler
	 *
	 * This function cycles through the entire array of mark points and
	 * matches any two points that are named identically (ending in "_start"
	 * and "_end" respectively).  It then compiles the execution times for
	 * all points and returns it as an array
	 *
	 * @return	array
	 */
	protected function _compile_benchmarks()
	{
		$benchmarks = array();

		foreach ($this->CI->benchmark->marker as $key => $val)
		{
			// We match the "end" marker so that the list ends
			// up in the order that it was defined
			if (preg_match("/(.+?)_end/i", $key, $match))
			{
				if (isset($this->CI->benchmark->marker[$match[1].'_end']) AND isset($this->CI->benchmark->marker[$match[1].'_start']))
				{
					$benchmarks[$match[1]] = $this->CI->benchmark->elapsed_time($match[1].'_start', $key);
				}
			}
		}

		foreach ($benchmarks as $key => $val)
		{
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$data[] = array(
				'field' => $key,
				'data' 	=> '<kbd>'.$val.'</kbd> ( seconds )'
			);
		}
		
		return array(
			'total' => count($data),
			'data'  => $data
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile Queries
	 *
	 * @return	string
	 */
	protected function _compile_database()
	{
		$dbs = array();
		$queries = array();
		$data = array();
		$configs = array();

		// Let's determine which databases are currently connected to
		foreach (get_object_vars($this->CI) as $CI_object)
		{
			if (is_object($CI_object) && is_subclass_of(get_class($CI_object), 'CI_DB') )
			{
				$dbs[] = $CI_object;
			}
		}
		
		// Key words we want bolded
		$highlight = array('SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 
							'INSERT','INTO', 'VALUES', 'UPDATE', 'OR&nbsp;', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 
							'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', '(', ')');

		$total_time = 0;
		foreach ($dbs as $db)
		{
			$configs = array(
				array(
					'field' => 'database', 
					'data' => $db->database
				),
				array(
					'field' => 'hostname', 
					'data' => $db->hostname
				),
				array(
					'field' => 'username', 
					'data' => $db->username
				),
				array(
					'field' => 'password', 
					'data' => $db->password
				),
				array(
					'field' => 'dbdriver', 
					'data' => $db->dbdriver
				),
				array(
					'field' => 'dbprefix', 
					'data' => $db->dbprefix
				),
				array(
					'field' => 'queries',
					'data' => count($db->queries)
				)
			);
			
			if (count($db->queries) != 0)
			{
				foreach ($db->queries as $key => $val)
				{
					$time = number_format($db->query_times[$key], 4);
					$total_time += $time;

					$val = highlight_code($val, ENT_QUOTES);

					foreach ($highlight as $bold)
					{
						$val = str_replace($bold, '<strong>'.$bold.'</strong>', $val);
					}
					
					$queries[] = array(
						'field' 	=> $val,
						'data' 	=> $time.' ( seconds )'
					);
				}
			}
		}

		$data = array(
			array(
				'field' => 'Number of Database Connections',
				'data'  => '<kbd>'.count($dbs).'</kbd>'
			),
			array(
				'field' => 'total number of queries',
				'data' => '<kbd>'.count($queries).'</kbd>'
			),
			array(
				'field' => 'total query execution time',
				'data' => '<kbd>'.$total_time.'</kbd> ( seconds )'
			)
		);

		return array(
			'total' => count($data),
			'data' => $data,
			'sub' => array(
				array(
					'title' => 'Database Queries',
					'data' => $queries,
					'section' => 'db_query'
				),
				array(
					'title' => 'Database Configurations',
					'data' => $configs,
					'section' => 'db_config'
				)
			)
		);
	}

	protected function _compile_request_data()
	{
		$types = array('get_data', 'post_data', 'cookie_data', 'file_data');

		$data = array();
		$sub  = array();

		foreach ($types as $type)
		{
			$label = '&#36;_'.strtoupper(str_replace('_data', '', $type));
			$method = "_compile_{$type}";

			$sub_data = call_user_func(array($this, $method));

			$data[] = array(
				'field' => "Number of $label variables",
				'data'  => '<kbd>'.$sub_data['total'].'</kbd>'
			);
			$sub[] = array(
				'title' => $label,
				'section'  => $type,
				'data' => $sub_data['data'],
				'header' => array('Name', 'Value')
			);
		}
		
		return array(
			'total' => count($data),
			'data' => $data,
			'sub' => $sub
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile $_GET Data
	 *
	 * @return	string
	 */
	protected function _compile_get_data()
	{
		$data = array();

		if (count($_GET) > 0)
		{
			foreach ($_GET as $key => $val)
			{
				if (is_array($val))
				{
					$val = "<pre>" . htmlspecialchars(stripslashes(print_r($val, TRUE))) . "</pre>";
				}
				else 
				{
					$val = htmlspecialchars(stripslashes($val));
				}
				
				$data[] = array(
					'field' 	=> $key,
					'data' 	=> $val
				);
			}
		}
		
		return array(
			'total' => count($data),
			'data'  => $data
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile $_POST Data
	 *
	 * @return string
	 */
	protected function _compile_post_data()
	{
		$data = array();
		if (count($_POST) > 0)
		{
			foreach ($_POST as $key => $val)
			{
				if (is_array($val))
				{
					$val = "<pre>" . htmlspecialchars(stripslashes(print_r($val, TRUE))) . "</pre>";
				}
				else 
				{
					$val = htmlspecialchars(stripslashes($val));
				}
				
				$data[] = array(
					'field' 	=> $key,
					'data' 	=> $val
				);
			}
		}
		
		return array(
			'total' => count($data),
			'data'  => $data
		);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Compile $_COOKIE Data
	 *
	 * @return string
	 */
	protected function _compile_cookie_data()
	{
		$data = array();
		if (isset($_COOKIE) AND count($_COOKIE) > 0)
		{
			foreach ($_COOKIE as $key => $val)
			{
				$val = unserialize($val);

				if (is_array($val))
				{
					$val = "<pre>" . htmlspecialchars(stripslashes(print_r($val, TRUE))) . "</pre>";
				}
				else 
				{
					$val = htmlspecialchars(stripslashes($val));
				}
				
				$data[] = array(
					'field' 	=> $key,
					'data' 	=> $val
				);
			}
		}
		
		return array(
			'total' => count($data),
			'data'  => $data
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile $_FILE Data
	 *
	 * @return string
	 */
	protected function _compile_file_data()
	{
		$data = array();
		if (isset($_FILE) AND count($_FILE) > 0)
		{
			foreach ($_FILE as $key => $val)
			{
				if ( ! is_numeric($key))
				{
					$key = " ' ".$key." ' ";
				}
				
				if (is_array($val))
				{
					$val = "<pre>" . htmlspecialchars(stripslashes(print_r($val, TRUE))) . "</pre>";
				}
				else 
				{
					$val = htmlspecialchars(stripslashes($val));
				}
				
				$data[] = array(
					'variable' 	=> '&#36;_FILE['.$key.']',
					'value' 	=> $val
				);
			}
		}
		
		return array(
			'total' => count($data),
			'data'  => $data
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Show query string
	 *
	 * @return	string
	 */
	protected function _compile_uri_string()
	{
		$data = array(
			array(
				'field' => 'Current URL',
				'data' 	=> anchor(current_url(), current_url())
			),
			array(
				'field' => 'Base URL',
				'data' 	=> anchor(base_url(), base_url())
			),
			array(
				'field' => 'URI segments',
				'data' 	=> uri_string()
			),
			
		);
		
		if(index_page() != '')
		{
			$data[] = array(
				'field' => 'Index page',
				'data' 	=> index_page()
			);	
		}
		
		return array(
			'total' => count($data),
			'data'  => $data
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Show the controller and function that were called
	 *
	 * @return	string
	 */
	protected function _compile_controller_info()
	{
		$data = array(
			array(
				'field' => 'Class',
				'data' 	=> $this->CI->router->fetch_class()
			),
			array(
				'field' => 'Current Method',
				'data' 	=> $this->CI->router->fetch_method()
			)
		);
		
		$class_methods = get_class_methods($this->CI->router->fetch_class());
		foreach ($class_methods as $method_name) {
			
			if( ! in_array($method_name, array('__construct', 'get_instance')))
			{
				$link = rtrim(base_url().index_page(), '/').'/'.
						$this->CI->router->fetch_class().
						($method_name == 'index' ? '' : '/'.$method_name);

				$methods[] = array(
					'field' => $method_name,
					'data' 	=> anchor($link, $link)
				);
			}
		}

		$data[] = array(
			'field' => 'Number of methods in class',
			'data' => count($methods)
		);

		return array(
			'total' => count($data),
			'data'  => $data,
			'sub' => array(
				array(
					'title' => 'All methods in class',
					'data' => $methods,
					'header' => array('Method Name', 'URL')
				)
			)
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile memory usage
	 *
	 * Display total used memory
	 *
	 * @return	string
	 */
	protected function _compile_memory_usage()
	{
		$m_func = array('memory_get_usage' => 'Memory Usage', 'memory_get_peak_usage' => 'Peak Memory Usage', 'memory_limit' => 'Memory Limit');

		foreach($m_func as $func => $label)
		{
			if(function_exists($func))
			{
				$mem = round(call_user_func($func)/1024/1024, 2).' MB';
			}
			elseif( ! $mem = @ini_get($func))
			{
				$mem = 'unknown';
			}

			$data[] = array(
				'field' => $label,
				'data' => '<kbd>'.$mem.'</kbd>'
			);
		}
		
		return array(
			'total' => count($data),
			'data' => $data
		);

	}

	// --------------------------------------------------------------------

	/**
	 * Compile header information
	 *
	 * Lists HTTP headers
	 *
	 * @return	string
	 */
	protected function _compile_request_headers($excludes = array())
	{
		$headers = array('HTTP_HOST', 
						 'HTTP_CONNECTION', 
						 'HTTP_CACHE_CONTROL',
						 'HTTP_USER_AGENT',
						 'HTTP_ACCEPT', 
						 'HTTP_ACCEPT_LANGUAGE', 
						 'HTTP_ACCEPT_ENCODING',
						 'HTTP_X_FORWARDED_FOR',
						 'SERVER_PORT', 
						 'SERVER_NAME', 
						 'SERVER_SOFTWARE', 
						 'REMOTE_ADDR', 
						 'REMOTE_HOST', 
						 'DOCUMENT_ROOT',
						 'SCRIPT_NAME', 
						 'QUERY_STRING', 
						 'GATEWAY_INTERFACE',
						 'SERVER_PROTOCOL', 
						 'REQUEST_METHOD',
						 'CONTENT_TYPE'
						 );

		$data = array();
		foreach($headers as $header)
		{
			$data[] = array(
				'field' => $header,
				'data' 	=> (array_key_exists($header, $_SERVER)) ? $_SERVER[$header] : ''
			);
		}

		return array(
			'total' => count($data),
			'data' => $data,
			'header' => array('Header', 'Content')
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile config information
	 *
	 * Lists developer config variables
	 *
	 * @return	string
	 */
	protected function _compile_config()
	{
		$data = array();

		foreach ($this->CI->config->config as $config=>$val)
		{
			if (is_array($val))
			{
				$val = print_r($val, TRUE);
			}

			$data[] = array(
				'field' => $config,
				'data'  => htmlspecialchars($val)
			);
		}

		return array(
			'total' => count($data),
			'data'  => $data,
			'header' => array('Name', 'Value')
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile session userdata
	 *
	 * @return 	string
	 */
	protected function _compile_session_data()
	{
		if ( ! isset($this->CI->session))
		{
			return array();
		}

		$data = array();

		foreach ($this->CI->session->all_userdata() as $key => $val)
		{
			if (is_array($val))
			{
				$val = print_r($val, TRUE);
			}

			$data[] = array(
				'field' => $key,
				'data'  => htmlspecialchars($val)
			);
		}

		return array(
			'total' => count($data),
			'data'  => $data,
			'header' => array('Name', 'Value')
		);
	}

	protected function _compile_loader()
	{
		$data = array();
		$classes = array();
		$files = array();
		$models = array();
		$helpers = array();
		$vars = array();

		foreach($this->CI->load->_ci_classes as $class => $object_name)
		{
			$classes[] = array(
				'field' => $class,
				'data'  => $object_name
			);
		}

		foreach($this->CI->load->_ci_loaded_files as $file)
		{
			$files[] = array(
				'field' => $file,
				'data'  => ''
			);
		}

		foreach($this->CI->load->_ci_models as $model)
		{
			$models[] = array(
				'field' => $model,
				'data'  => APPPATH.'models/'.$model.'.php',
			);
		}

		foreach($this->CI->load->_ci_helpers as $helper => $loaded)
		{
			$file = '<var>APPPATH</var>/helpers/'.config_item('subclass_prefix').$helper.'.php';
			$helpers[] = array(
				'field' => $helper,
				'data'  => is_file($file) ? $file : '<var>BASEPATH</var>/helpers/'.$helper.'.php'
			);
		}

		foreach($this->CI->load->_ci_cached_vars as $key => $val)
		{
			$vars[] = array(
				'field' => $key,
				'data'  => $val
			);
		}

		$data = array(
			array(
				'field' => 'Variables',
				'data'  => count($vars)
			),
			array(
				'field' => 'Models Loaded',
				'data'  => count($models)
			),
			array(
				'field' => 'Helpers Loaded',
				'data'  => count($helpers)
			),
			array(
				'field' => 'Libraries Loaded',
				'data'  => count($classes)
			),
			array(
				'field' => 'Sparks Enabled',
				'data' => defined('SPARKPATH') ? '<dfn>Yes</dfn>' : '<kbd>No</kbd>'
			)
		);

		return array(
			'total' => count($data),
			'data'  => $data,
			'sub'  => array(
				array(
					'title' => 'Variables',
					'data'  => $vars,
					'section' => 'vars',
					'header' => array('Variable Name', 'Value')
				),
				array(
					'title' => 'Models',
					'data'  => $models,
					'section' => 'models',
					'header' => array('Name', 'File')
				),
				array(
					'title' => 'Helpers',
					'data'  => $helpers,
					'section' => 'helpers',
					'header' => array('Name', 'File')
				),
				array(
					'title' => 'Libraries',
					'data'  => $classes,
					'section' => 'libraries',
					'header' => array('Class', 'Object Name')
				),
				array(
					'title' => 'Files',
					'data'  => $files,
					'section' => 'files'
				)
			)
		);
	}

	// --------------------------------------------------------------------

	private function _load_section($_section)
	{
		$_view = 'section';
		$_profile = call_user_func(array($this, "_compile_$_section"));

		$_data = array(
			'section' => $_section,
			'profile' => $_profile,
		);

		if(empty($_profile))
		{
			return lang('profiler_no_'.$_section);
		}

		return $this->_load_view('section', $_data);
	}

	// --------------------------------------------------------------------

	private function _load_view($view, $data = array())
	{
		if( ! empty($data) AND is_array($data))
		{
			extract($data);
		}

		ob_start();
		include($this->_profiler_path.$view.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

	// --------------------------------------------------------------------

	private function _check_environment()
	{
		if($this->CI->input->is_ajax_request() OR $this->CI->input->is_cli_request())
		{
			return FALSE;
		}

		return TRUE;
	}

}