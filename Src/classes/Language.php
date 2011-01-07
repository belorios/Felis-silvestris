<?php

	class Language {
		
		private $lang, $availLang;
		
		public function __construct($lang) {
			$this->availableLang();
			$this->setLang($lang);
		}
		
		private function availableLang() {
			$this->availLang = array(
				"en" => "en_US.php",
				"sv" => "sv_SE.php",
			);
		}
		
		public function setLang($lang) {
		
			if (array_key_exists($lang, $this->availLang)) {
				$this->lang = $this->availLang[$lang];
			}
			else {
				$this->lang = $this->availLang("en");
				throw new Exception("Couldnt set language");
			}
			
		}
		
		public function getLangFiles($module=false) {
			
			global $lang;
			
			require_once(PATH_LANG . $this->lang);
			
			if ($module != false) {
				$langTmp = $lang;
				require_once($module . $this->lang);
				$lang  = array_merge($langTmp, $lang);
				
			}
		}
	}
