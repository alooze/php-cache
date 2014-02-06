<?php
namespace Modx\Ext\Cache\Backend;
use Modx\Ext\Cache\ICacheBackend;
use Modx\Ext\Cache\Cache as Cache;

class File extends ICacheBackend {

	protected $time;

	public function __construct($options){
		$this->prefix = $options['root'] . '/' . $options['prefix'] . '/';
		$this->time = time();
	}
	
	public function retrieve($key){
		$file = $this->prefix . $key;
		if (!file_exists($file)) return null;
		
		$content = explode('^', file_get_contents($file), 2);
		if ($content[0] && $content[0] < $this->time){
			$this->erase(array($key));
			return null;
		}

		return $content[1];

	}
	
	public function store($key, $content, $ttl = null){
		$file = $this->prefix . $key;
		
		\Modx\Ext\Cache\write($file, ($ttl ? $this->time + $ttl : 0) . '^' . $content);
	}
	
	public function erase($keys){
		foreach ($keys as $key)
			if (file_exists($file = $this->prefix . $key))
				unlink($file);
	}

	public function eraseAll(){
		\Modx\Ext\Cache\unlink($this->prefix);
	}
	
}

Cache::register('File');