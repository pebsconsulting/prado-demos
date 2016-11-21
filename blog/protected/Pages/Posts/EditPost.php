<?php
/**
 * EditPost class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link https://github.com/pradosoft/prado4
 * @copyright Copyright &copy; 2006-2016 The PRADO Group
 * @license https://github.com/pradosoft/prado4/blob/master/LICENSE
 */

/**
 * EditPost class
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link https://github.com/pradosoft/prado4
 * @copyright Copyright &copy; 2006-2016 The PRADO Group
 * @license https://github.com/pradosoft/prado4/blob/master/LICENSE
 */
class EditPost extends BlogPage
{
	private $_postRecord=null;

	public function onInit($param)
	{
		parent::onInit($param);
		$id=TPropertyValue::ensureInteger($this->Request['id']);
		$this->_postRecord=$this->DataAccess->queryPostByID($id);
		if($this->_postRecord===null)
			throw new BlogException(500,'post_id_invalid',$id);
		// only the author and admin can edit the post
		if(!$this->User->IsAdmin && $this->User->ID!==$this->_postRecord->AuthorID)
			throw new BlogException(500,'post_edit_disallowed',$id);
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->IsPostBack)
		{
			$postRecord=$this->_postRecord;
			$this->Title->Text=$postRecord->Title;
			$this->Content->Text=$postRecord->Content;
			$this->DraftMode->Checked=$postRecord->Status===PostRecord::STATUS_DRAFT;
			$this->Categories->DataSource=$this->DataAccess->queryCategories();
			$this->Categories->dataBind();
			$cats=$this->DataAccess->queryCategoriesByPostID($postRecord->ID);
			$catIDs=array();
			foreach($cats as $cat)
				$catIDs[]=$cat->ID;
			$this->Categories->SelectedValues=$catIDs;
		}
	}

	public function saveButtonClicked($sender,$param)
	{
		if($this->IsValid)
		{
			$postRecord=$this->_postRecord;
			$postRecord->Title=$this->Title->SafeText;
			$postRecord->Content=$this->Content->SafeText;
			if($this->DraftMode->Checked)
				$postRecord->Status=PostRecord::STATUS_DRAFT;
			else if(!$this->User->IsAdmin && TPropertyValue::ensureBoolean($this->Application->Parameters['PostApproval']))
				$postRecord->Status=PostRecord::STATUS_PENDING;
			else
				$postRecord->Status=PostRecord::STATUS_PUBLISHED;
			$postRecord->ModifyTime=time();
			$cats=array();
			foreach($this->Categories->SelectedValues as $value)
				$cats[]=TPropertyValue::ensureInteger($value);
			$this->DataAccess->updatePost($postRecord,$cats);
			$this->gotoPage('Posts.ViewPost',array('id'=>$postRecord->ID));
		}
	}
}

