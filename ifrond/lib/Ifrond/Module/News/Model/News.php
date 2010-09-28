<?php
class Ifrond_Module_News_Model_News extends Ifrond_Model_Item
{
	
	protected $_gallery = null;
	
	public function setTags($tagsList) {
		if (sizeof($tagsList) > 0) {
			$tags = $this->getModelMapperTag();
			$this->tags = $tags->reAttach($tagsList, $this->id);
		}
	}

	public function getModelMapperTag() {
		$d = new Ifrond_Module_Tag_Model_Mapper_Tag();
		$d->setPmodule('news');
		return $d;
	}

	public function getTags() {
		$tags = $this->getModelMapperTag();
		$tagsList = $tags->getAttached($this->id);
		if (sizeof($tagsList) > 0) {
			$this->tags = $tagsList;
		}
		return $tagsList;

	}

	public function extend() {		
		$this->getTags();
		$this->getTopic();		
	}

	public function getTopic() {
		if ($this->id > 0) {
			$topicMapper = $this->getModelTopic();
			//_p($this->id);
			$topic = $topicMapper->find($this->pid);
			$this->topic = $topic;
		}
		return $topic;
	}
	
	public function getGallery() {
		if ($this->_gallery == null) {
			if ($this->id > 0) {
				$galleryMapper = $this->getModelGallery();
				$gallery = $galleryMapper->getByNews($this->id);						
				$this->_gallery = $gallery; 
			}
		}
		return $this->_gallery;
	}

	public function getModelTopic() {
		$d = new Ifrond_Module_Ref_Model_Mapper_Ref();
		return $d;
	}
	
	public function getModelGallery() {
		$d = new Ifrond_Module_News_Model_Mapper_Gallery();
		return $d;
	}

}