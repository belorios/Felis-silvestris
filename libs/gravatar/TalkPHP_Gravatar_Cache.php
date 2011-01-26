<?php

	class TalkPHP_Gravatar_Cache
	{
		private $m_szFilename;
		
		const GRAVATAR_CACHE_FILENAME = 'libs/gravatar/cache/%s_%d.jpg';
		const GRAVATAR_CACHE_EXPIRE = 5;
		
		public function isValidCache($szFilename, $iSize)
		{
			$this->m_szFilename = sprintf(self::GRAVATAR_CACHE_FILENAME, $szFilename, $iSize);
			
			if(!file_exists($this->m_szFilename))
			{
				return false;
			}
			
			if(filemtime($this->m_szFilename) < strtotime('-' . self::GRAVATAR_CACHE_EXPIRE . ' days') )
			{
				return false;
			}
			
			return true;
		}
		
		public function getAvatar()
		{
			return "{$this->m_szFilename}";
		}
	}

?>