<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of {$title} categories
 *
 * @author {$author_name} <{$author_email}>
 */
class Frontend{$camel_case_name}Category extends FrontendBaseBlock
{
	/**
	 * The items and category
	 *
	 * @var	array
	 */
	private $items, $category;

	/**
	 * The pagination array
	 * It will hold all needed parameters, some of them need initialization.
	 *
	 * @var	array
	 */
	protected $pagination = array('limit' => 10, 'offset' => 0, 'requested_page' => 1, 'num_items' => null, 'num_pages' => null);

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 */
	private function getData()
	{
		if($this->URL->getParameter(0) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get category
		$this->category = Frontend{$camel_case_name}Model::getCategory($this->URL->getParameter(0));
		if(empty($this->category)) $this->redirect(FrontendNavigation::getURL(404));

		// requested page
		$requestedPage = $this->URL->getParameter('page', 'int', 1);

		// set URL and limit
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('{$underscored_name}');
		$this->pagination['limit'] = FrontendModel::getModuleSetting('{$underscored_name}', 'overview_num_items', 10);

		// populate count fields in pagination
		$this->pagination['num_items'] = Frontend{$camel_case_name}Model::getCategoryCount($this->category['id']);
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items'] / $this->pagination['limit']);

		// num pages is always equal to at least 1
		if($this->pagination['num_pages'] == 0) $this->pagination['num_pages'] = 1;

		// redirect if the request page doesn't exist
		if($requestedPage > $this->pagination['num_pages'] || $requestedPage < 1) $this->redirect(FrontendNavigation::getURL(404));

		// populate calculated fields in pagination
		$this->pagination['requested_page'] = $requestedPage;
		$this->pagination['offset'] = ($this->pagination['requested_page'] * $this->pagination['limit']) - $this->pagination['limit'];

		// get items
		$this->items = Frontend{$camel_case_name}Model::getAllByCategory($this->category['id'], $this->pagination['limit'], $this->pagination['offset']);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		/**
		 * @TODO add specified image
		 * $this->header->addOpenGraphImage(FRONTEND_FILES_URL . '/{$underscored_name}/images/source/' . $this->record['image']);
		 */

		// add additional OpenGraph data
		$this->header->addOpenGraphData('title', $this->record['meta_title'], true);
		$this->header->addOpenGraphData('type', 'article', true);
		$this->header->addOpenGraphData('url', SITE_URL . FrontendNavigation::getURLForBlock('{$underscored_name}', 'detail') . '/' . $this->record['url'], true);
		$this->header->addOpenGraphData('site_name', FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE), true);
		$this->header->addOpenGraphData('description', $this->record['title'], true);

		/**
		 * @TODO add the responsible twitter account and image
		 * $this->addMetaData(array('property' => 'twitter:creator', 'content' => '@vreewijs'), true, 'property');
		 * $this->addMetaData(array('property' => 'twitter:site', 'content' => '@vreewijs'), true, 'property');
		 * $this->addMetaData(array('property' => 'twitter:image', 'content' => '"http://wijs.be/frontend/files/blog/images/source/facetnavigatie-performantie.jpg'), true, 'property');
		 */
		$this->header->addMetaData(array('property' => 'twitter:card', 'content' => 'summary'), true, 'property');
		$this->header->addMetaData(array('property' => 'twitter:url', 'content' => SITE_URL . FrontendNavigation::getURLForBlock('{$underscored_name}', 'detail') . '/' . $this->record['url']), true, 'property');
		$this->header->addMetaData(array('property' => 'twitter:title', 'content' => $this->record['meta_title']), true, 'property');
		$this->header->addMetaData(array('property' => 'twitter:description', 'content' => $this->record['meta_title']), true, 'property');

		// add into breadcrumb
		$this->breadcrumb->addElement($this->category['meta_title']);

		// set meta
		$this->header->setPageTitle($this->category['meta_title'], ($this->category['meta_title_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->category['meta_description'], ($this->category['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->category['meta_keywords'], ($this->category['meta_keywords_overwrite'] == 'Y'));

		// advanced SEO-attributes
		if(isset($this->category['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->category['meta_data']['seo_index']));
		if(isset($this->category['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->category['meta_data']['seo_follow']));

		// assign items
		$this->tpl->assign('items', $this->items);

		// parse the pagination
		$this->parsePagination();
	}
}