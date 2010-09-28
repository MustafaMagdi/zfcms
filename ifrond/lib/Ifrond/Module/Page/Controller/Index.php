<?php
class Ifrond_Module_Page_Controller_Index extends Ifrond_Controller_Action_Public
{
    private $_flagDirect = false;
    /* @var pages Ifrond_Module_Page_Model_Mapper_Page */
    protected $pages;
    
	public function getModelMapperPage() {
		$d = new Ifrond_Module_Page_Model_Mapper_Page();
		return $d;
	}
	
    public function postInit ()
    {       
    	$this->pages = $this->getModelMapperPage();
    }
    
    public function setHead($page, $parents) {
		if ($page->seo_title != '')  {
			$this->view->headTitle($page->seo_title, 'SET');
		} else {
			if (isset($parents[0])) $this->view->headTitle($parents[0]->title, 'SET');
			$this->view->headTitle($page->title);
			$this->view->headTitle()->setSeparator(' / ');
		}		
		if ($page->seo_description != '')  {
			$this->view->headMeta()->setName('description', $page->seo_description);
		}		
		if ($page->seo_keywords != '')  {
			$this->view->headMeta()->setName('keywords', $page->seo_keywords);
		}		
    }
    
	public function cleanTexts($page) {
		$page->subtitle = stripslashes(trim(strip_tags($page->subtitle))); 
		$page->title = stripslashes(trim(strip_tags($page->title)));
		if ($page->title == '') $page->title = 'Без названия';
		$page->text = stripslashes($page->text);
		return $page;
    }
    
    public function permlinkAction ()
    {
    	$this->_helper->viewRenderer('item');
    	$baseURL = clearStr($this->_getParam('baseURL', -1));
    	$util = $this->_getParam('util', '');		
		if ($util === 'print') {
			$this->_helper->layout()->setLayout('print');
			$this->view->print = true;
		} else {
			$this->view->print = false;
		}
    	$id = intval($this->_getParam('id', 0));
    	if ($id == 0) {
    		$this->_helper->viewRenderer('404');
    		return true;
    	}    	
    	$page = $this->pages->find($id);
    	if (!$page) {
    		$this->_helper->viewRenderer('404');
    		return true;
    	}    	
    	$page = $this->cleanTexts($page);
        $parents = $page->getParents();
        if ($baseURL == -1) {
	        if ($page->is_cat == 0) {
	        	$baseURL = $this->pages->getTree()->getTreePathTo($page->pid);
	        	$baseURL .= $page->shortcut.'/';
	        } else {
	        	$baseURL = $this->pages->getTree()->getTreePathTo($page->id);        	
	        }
	        $baseURL = '/'.$baseURL;
        }
        //_p($baseURL);
        $this->view->page = $page;
		$this->view->childs = $page->getChilds();
        $this->view->extends = $page->extend();
        $this->view->parents = $parents;
        $this->view->baseURL = $baseURL;
        $this->setHead($page, $parents);
    }
    
    public function indexAction ()
    {
        $c = $this->getRequest()->getParam('controller');
        if ($this->getRequest()->getParam('controller') == 'index' && ! $this->_flagDirect) {
            $this->_helper->layout()->setLayout('index');
        } else {            
            $path = $this->getRequest()->getRequestUri();  
            $path = $this->clearPath($path);          
            $page = $this->pages->getByPath($path);
            if ($page == false) {       
                //$this->getResponse()->setHttpResponseCode(404)->sendHeaders();                       
                $this->_helper->viewRenderer('404');                
            } else {
            	$page = $this->cleanTexts($page);
            	$parents = $page->parents();
                $this->_helper->viewRenderer('item');
                $this->view->page = $page;
                $this->view->childs = $page->childs();
                $this->view->extends = $page->extend();
                $this->view->parents = $parents;
                $this->view->baseURL = $path;
                $this->setHead($page, $parents);
            }           
        }
    }
    public function clearPath($path) {
        $path = str_replace('.html', '/', $path);
        $path = str_replace('.htm', '/', $path);
        $path = str_replace('//', '/', $path);
    	return $path;
    } 
    public function __call ($method, $args)
    {
        $this->_flagDirect = true;
        return $this->_forward('index');
    }    
    public function testAction ()
    {
        $pages = new Page_Model_Mapper_Page();
        $pageList = $pages->getAllByPid(array('value' => 8 , 'limit' => 30 , 'order' => 'indate DESC'));
        // _p($pageList[0]);
        $p = $pageList[0]->parent()->childs(array('limit' => 1));
        $this->view->title = $p[0]->title;
        $p[0]->title = 'sdfsd';
        $this->view->titleNew = $p[0]->title;
        $t = $pages->getByPath('/press/news/news20100216/');
        $parents = $t->parents();
        $extends = $parents[0]->extend();
        echo '<pre>';
        print_r($extends['files'][0]->title);
        echo '</pre>';
        die();
        if ($t) {
            $this->view->title = $t->title;
        }
    }
}