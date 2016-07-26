<?php

namespace SaltedHerring;

class Grid {
	public static function make($name, $label = '', $source, $sortable = true, $gridHeaderType = 'GridFieldConfig_RecordEditor') {
		/*
		GridFieldConfig_Base
		GridFieldConfig_RecordViewer
		GridFieldConfig_RecordEditor
		GridFieldConfig_RelationEditor
		*/
		if ($label == '') { $label = $name; }
		$grid = new \GridField($name, $label, $source);
		$config = $gridHeaderType::create();
		$config->removeComponentsByType('GridFieldPaginator')
				->addComponents(
					new \GridFieldPaginatorWithShowAll(30)
				);
		if ($sortable) {
			$config->addComponents(
				$sortable = new \GridFieldOrderableRows('SortOrder')
			);
		}
		
		$grid->setConfig($config);
		return $grid;
	}
}