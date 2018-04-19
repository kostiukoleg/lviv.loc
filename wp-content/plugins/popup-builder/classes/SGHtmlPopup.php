<?php
require_once(dirname(__FILE__).'/SGPopup.php');

class SGHtmlPopup extends SGPopup {
	public $content;

	public function setContent($content) {
		$this->content = $content;
	}
	public function getContent() {
		return $this->content;
	}
	public static function create($data, $obj = null) {
		$obj = new self();

		$obj->setContent($data['html']);

		return parent::create($data, $obj);
	}
	public function save($data = array()) {

		$editMode = $this->getId()?true:false;

		$res = parent::save($data);
		if ($res===false) return false;

		$sgHtmlPopup = $this->getContent();

		global $wpdb;
		if ($editMode) {
			$sgHtmlPopup = stripslashes($sgHtmlPopup);
			$sql = $wpdb->prepare("UPDATE ". $wpdb->prefix ."sg_html_popup SET content=%s WHERE id=%d",$sgHtmlPopup,$this->getId());
			$res = $wpdb->query($sql);
		}
		else {

			$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix ."sg_html_popup (id, content) VALUES (%d,%s)",$this->getId(),$sgHtmlPopup);
			$res = $wpdb->query($sql);
		}
		return $res;
	}

	protected function setCustomOptions($id) {
		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."sg_html_popup WHERE id = %d",$id);
		$arr = $wpdb->get_row($st,ARRAY_A);
		$this->setContent($arr['content']);
	}

	private function filterContentForAutoPlayIframe($content)
	{
		// $match array 0 => content 1 => Iframe url
		preg_match('/<iframe.*?src="(.*?)".*?<\/iframe>/', $content, $match);

		if(empty($match[1])) {
			return $content;
		}
		$iframeUrl = $match[1];

		$popupOptions = $this->getOptions();
		$popupOptions = json_decode($popupOptions, true);

		if(empty($popupOptions)) {
			return $content;
		}
		$popupOptions['htmlIframeUrl'] = $iframeUrl;
		$popupOptions = json_encode($popupOptions);
		$this->setOptions($popupOptions);

		return str_replace($iframeUrl,' ',$content);
	}

	protected function getExtraRenderOptions() {
		$content = trim($this->getContent());
		$hasShortcode = $this->hasPopupContentShortcode($content);
		$popupId = (int)$this->getId();

		if($hasShortcode) {
			$content = $this->improveContent($content);
		}
		$content = $this->filterContentForAutoPlayIframe($content);

		$this->sgAddPopupContentToFooter($content, $popupId);

		return array('html' => $content);
	}

	public  function render() {
		return parent::render();
	}
}
