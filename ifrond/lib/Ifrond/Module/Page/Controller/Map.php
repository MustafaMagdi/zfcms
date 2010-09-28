<?php
class Ifrond_Module_Page_Controller_Map extends Ifrond_Controller_Action_Public
{
    private $_flagDirect = false;
    /* @var pages Ifrond_Module_Page_Model_Mapper_Page */
    protected $pages;
    
	public function getModelMapperPage() {
		$d = new Ifrond_Module_Page_Model_Mapper_Page();
		return $d;
	}
	
	public function indexAction ()
    {
        $pagesMapper = $this->getModelMapperPage();
        $tree = $pagesMapper->getTree();
        $this->view->tree = $tree->getTree();        
    }   
    
}