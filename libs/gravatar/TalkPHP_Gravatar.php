<?php

	define('GRAVATAR_RATING_G', 'G');
	define('GRAVATAR_RATING_PG', 'PG');
	define('GRAVATAR_RATING_R', 'R');
	define('GRAVATAR_RATING_X', 'X');

	class TalkPHP_Gravatar
	{
		private $m_szImage;
		private $m_szEmail;
		private $m_iSize;
		private $m_szRating;
		
		const GRAVATAR_SITE_URL = 'http://www.gravatar.com/avatar.php?gravatar_id=%ssize=%sdefault=%srating=%s';
		
		public function __construct()
		{
			$this->m_iSize = 80;
			$this->m_szRating = 'R';
			$this->m_szImage = '';
		}
		
		public function getAvatar()
		{
			$pCache = new TalkPHP_Gravatar_Cache();
			
			if(!$pCache instanceof TalkPHP_Gravatar_Cache)
			{
				throw new Exception('Cache class is not an instance of TalkPHP_Gravatar_Cache');
			}
			
			if($pCache->isValidCache($this->m_szEmail, $this->m_iSize))
			{
				return PATH_SITE_LOC . "/{$pCache->getAvatar()}";
			}
			
			$szImage = (string) sprintf
			(
				self::GRAVATAR_SITE_URL,
				$this->m_szEmail,
				$this->m_iSize,
				$this->m_szImage,
				$this->m_szRating
			);
			
			file_put_contents($pCache->getAvatar(), file_get_contents($szImage));
			
			return $szImage;
		}
		
		public function setImage($szImage)
		{
			$this->m_szImage = (string) urlencode($szImage);
			return $this;
		}
		
		public function setEmail($szEmail)
		{
			$this->m_szEmail = (string) md5($szEmail);
			return $this;
		}
		
		public function setSize($iSize)
		{
			$this->m_iSize = (int) $iSize;
			return $this;
		}
		
		public function setRating($szRating)
		{
			$this->m_szRating = (string) $szRating;
			return $this;
		}
	}

?>