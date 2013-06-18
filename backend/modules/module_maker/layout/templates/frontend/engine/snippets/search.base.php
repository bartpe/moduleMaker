
	/**
	* Parse the search results for this module
	*
	* Note: a module's search function should always:
	* 		- accept an array of entry id's
	* 		- return only the entries that are allowed to be displayed, with their array's index being the entry's id
	*
	*
	* @param array $ids The ids of the found results.
	* @return array
	*/
	public static function search(array $ids)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*, m.url
			FROM {$underscored_name} AS i
			INNER JOIN meta AS m ON i.meta_id = m.id
			WHERE i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
			array(FRONTEND_LANGUAGE), 'id'
		);

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = FrontendNavigation::getURLForBlock('{$underscored_name}', 'detail') . '/' . $item['url'];
		}

		// return
		return $items;
	}